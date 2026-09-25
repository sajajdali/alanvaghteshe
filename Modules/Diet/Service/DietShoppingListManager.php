<?php

namespace Modules\Diet\Service;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Modules\Diet\app\Jobs\GenerateDietShoppingListJob;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietShoppingList;
use Modules\Diet\Enum\ShoppingListPeriodEnum;
use Modules\Diet\Enum\ShoppingListStatusEnum;
use RuntimeException;

class DietShoppingListManager
{
    public function __construct(
        private readonly DietShoppingSnapshotExtractor $extractor
    ) {
    }

    /**
     * @return array{shopping_list: DietShoppingList, created: bool}
     */
    public function generate(
        DietRequest $dietRequest,
        ShoppingListPeriodEnum $period,
        int $userId
    ): array {
        $snapshot = $this->extractor->extract($dietRequest, $period, $userId);

        try {
            $result = DB::transaction(function () use ($dietRequest, $period, $userId, $snapshot): array {
                $shoppingList = DietShoppingList::query()->firstOrCreate(
                    [
                        'diet_request_id' => $dietRequest->getKey(),
                        'period' => $period->value,
                        'input_hash' => $snapshot['input_hash'],
                    ],
                    [
                        'user_id' => $userId,
                        'start_date' => $snapshot['start_date'],
                        'end_date' => $snapshot['end_date'],
                        'status' => ShoppingListStatusEnum::PENDING,
                    ]
                );

                return [
                    'shopping_list' => $shoppingList,
                    'created' => $shoppingList->wasRecentlyCreated,
                ];
            });
        } catch (UniqueConstraintViolationException) {
            $result = [
                'shopping_list' => DietShoppingList::query()
                    ->where('diet_request_id', $dietRequest->getKey())
                    ->where('period', $period->value)
                    ->where('input_hash', $snapshot['input_hash'])
                    ->firstOrFail(),
                'created' => false,
            ];
        }

        // A pending row may outlive its queued job after a worker/deployment
        // interruption. Dispatching it again is safe because the job is unique
        // per shopping-list ID while it is queued or running.
        if ($result['shopping_list']->status === ShoppingListStatusEnum::PENDING) {
            GenerateDietShoppingListJob::dispatch($result['shopping_list']->getKey())
                ->onQueue('ai-shopping-list')
                ->afterCommit();
        }

        return $result;
    }

    public function findCurrent(
        DietRequest $dietRequest,
        ShoppingListPeriodEnum $period,
        int $userId
    ): ?DietShoppingList {
        $snapshot = $this->extractor->extract($dietRequest, $period, $userId);

        return DietShoppingList::query()
            ->where('user_id', $userId)
            ->where('diet_request_id', $dietRequest->getKey())
            ->where('period', $period->value)
            ->where('input_hash', $snapshot['input_hash'])
            ->latest('id')
            ->first();
    }

    public function retry(DietShoppingList $shoppingList, int $userId): DietShoppingList
    {
        if ((int) $shoppingList->user_id !== $userId) {
            throw new RuntimeException('Shopping list does not belong to the authenticated user.');
        }

        if ($shoppingList->status !== ShoppingListStatusEnum::FAILED) {
            throw new RuntimeException('Only failed shopping lists can be retried.');
        }

        $snapshot = $this->extractor->extract(
            $shoppingList->dietRequest,
            $shoppingList->period,
            $userId
        );

        if (!hash_equals($shoppingList->input_hash, $snapshot['input_hash'])) {
            throw new RuntimeException('The diet changed. Generate a new shopping list instead.');
        }

        $shoppingList->forceFill([
            'status' => ShoppingListStatusEnum::PENDING,
            'result' => null,
            'model' => null,
            'input_tokens' => null,
            'output_tokens' => null,
            'error_message' => null,
            'started_at' => null,
            'generated_at' => null,
        ])->save();

        GenerateDietShoppingListJob::dispatch($shoppingList->getKey())
            ->onQueue('ai-shopping-list')
            ->afterCommit();

        return $shoppingList->refresh();
    }
}
