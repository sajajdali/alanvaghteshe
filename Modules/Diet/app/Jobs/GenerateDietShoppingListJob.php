<?php

namespace Modules\Diet\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Diet\app\Events\ShoppingListStatusChanged;
use Modules\Diet\Entities\DietShoppingList;
use Modules\Diet\Enum\ShoppingListStatusEnum;
use Modules\Diet\Service\DietShoppingSnapshotExtractor;
use Modules\Diet\Service\OpenAiShoppingListService;
use RuntimeException;
use Throwable;

class GenerateDietShoppingListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 240;

    /** @var array<int, int> */
    public array $backoff = [5, 15, 30];

    public function __construct(public readonly int $shoppingListId)
    {
    }

    public function handle(
        DietShoppingSnapshotExtractor $extractor,
        OpenAiShoppingListService $openAi
    ): void {
        $shoppingList = DietShoppingList::query()->findOrFail($this->shoppingListId);

        if ($shoppingList->status === ShoppingListStatusEnum::READY) {
            return;
        }

        $shoppingList->forceFill([
            'status' => ShoppingListStatusEnum::PROCESSING,
            'started_at' => now(),
            'error_message' => null,
        ])->save();

        event(new ShoppingListStatusChanged($shoppingList->fresh()));

        $snapshot = $extractor->extract(
            $shoppingList->dietRequest,
            $shoppingList->period,
            (int) $shoppingList->user_id,
            $shoppingList->start_date->copy()->subDay()
        );

        if (!hash_equals($shoppingList->input_hash, $snapshot['input_hash'])) {
            throw new RuntimeException('The prescribed diet changed before shopping list processing started.');
        }

        if ($snapshot['meals'] === []) {
            $shoppingList->forceFill([
                'status' => ShoppingListStatusEnum::READY,
                'result' => [
                    'schema_version' => 1,
                    'engine' => 'empty',
                    'title' => $snapshot['period'] === 'daily'
                        ? 'سبد خرید فردا'
                        : 'سبد خرید هفت روز آینده',
                    'total_items' => 0,
                    'checked_items' => 0,
                    'progress_percent' => 0,
                    'groups' => [],
                ],
                'generated_at' => now(),
            ])->save();

            event(new ShoppingListStatusChanged($shoppingList->fresh()));
            return;
        }

        $generated = $openAi->generate($snapshot);

        $shoppingList->forceFill([
            'status' => ShoppingListStatusEnum::READY,
            'result' => $generated['result'],
            'model' => $generated['model'],
            'input_tokens' => $generated['input_tokens'],
            'output_tokens' => $generated['output_tokens'],
            'generated_at' => now(),
        ])->save();

        event(new ShoppingListStatusChanged($shoppingList->fresh()));
    }

    public function failed(?Throwable $exception): void
    {
        DietShoppingList::query()->whereKey($this->shoppingListId)->update([
            'status' => ShoppingListStatusEnum::FAILED->value,
            'error_message' => mb_substr(
                $exception?->getMessage() ?? 'Shopping list generation failed.',
                0,
                2000
            ),
        ]);

        $shoppingList = DietShoppingList::query()->find($this->shoppingListId);
        if ($shoppingList) {
            event(new ShoppingListStatusChanged($shoppingList));
        }
    }
}
