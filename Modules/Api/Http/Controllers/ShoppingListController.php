<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\CheckDietShoppingListItemRequest;
use Modules\Api\Http\Requests\GenerateDietShoppingListRequest;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietShoppingList;
use Modules\Diet\Enum\ShoppingListStatusEnum;
use Modules\Diet\Service\DietShoppingListManager;
use Modules\Diet\Service\DietShoppingListResultManager;
use RuntimeException;

class ShoppingListController extends Controller
{
    use ApiHandlerTrait;

    public function generate(
        GenerateDietShoppingListRequest $request,
        DietRequest $dietRequest,
        DietShoppingListManager $manager
    ): JsonResponse {
        if ((int) $dietRequest->user_id !== (int) auth()->id()) {
            return $this->notFound();
        }

        try {
            $result = $manager->generate($dietRequest, $request->period(), (int) auth()->id());
        } catch (RuntimeException $exception) {
            return $this->badRequest(['message' => $exception->getMessage()]);
        }
        $shoppingList = $result['shopping_list']->fresh();

        return $shoppingList->status === ShoppingListStatusEnum::READY
            ? $this->ok($this->responseData($shoppingList))
            : $this->accepted($this->responseData($shoppingList));
    }

    public function current(
        GenerateDietShoppingListRequest $request,
        DietRequest $dietRequest,
        DietShoppingListManager $manager
    ): JsonResponse {
        if ((int) $dietRequest->user_id !== (int) auth()->id()) {
            return $this->notFound();
        }

        try {
            $shoppingList = $manager->findCurrent($dietRequest, $request->period(), (int) auth()->id());
        } catch (RuntimeException $exception) {
            return $this->badRequest(['message' => $exception->getMessage()]);
        }

        return $shoppingList
            ? $this->ok($this->responseData($shoppingList))
            : $this->notFound();
    }

    public function show(DietRequest $dietRequest, DietShoppingList $shoppingList): JsonResponse
    {
        if (
            (int) $dietRequest->user_id !== (int) auth()->id()
            || (int) $shoppingList->user_id !== (int) auth()->id()
            || (int) $shoppingList->diet_request_id !== (int) $dietRequest->getKey()
        ) {
            return $this->notFound();
        }

        return $this->ok($this->responseData($shoppingList));
    }

    public function check(
        CheckDietShoppingListItemRequest $request,
        DietRequest $dietRequest,
        DietShoppingList $shoppingList,
        DietShoppingListResultManager $resultManager
    ): JsonResponse {
        if (!$this->owns($dietRequest, $shoppingList)) {
            return $this->notFound();
        }

        $shoppingList = $resultManager->setChecked(
            $shoppingList,
            $request->validated('item_key'),
            (bool) $request->validated('is_checked')
        );

        return $this->ok($this->responseData($shoppingList));
    }

    public function retry(
        DietRequest $dietRequest,
        DietShoppingList $shoppingList,
        DietShoppingListManager $manager
    ): JsonResponse {
        if (!$this->owns($dietRequest, $shoppingList)) {
            return $this->notFound();
        }

        try {
            $shoppingList = $manager->retry($shoppingList, (int) auth()->id());
        } catch (RuntimeException $exception) {
            return $this->badRequest(['message' => $exception->getMessage()]);
        }

        return $this->accepted($this->responseData($shoppingList));
    }

    private function owns(DietRequest $dietRequest, DietShoppingList $shoppingList): bool
    {
        return (int) $dietRequest->user_id === (int) auth()->id()
            && (int) $shoppingList->user_id === (int) auth()->id()
            && (int) $shoppingList->diet_request_id === (int) $dietRequest->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    private function responseData(DietShoppingList $shoppingList): array
    {
        return [
            'status' => $shoppingList->status->value,
            'shopping_list_id' => $shoppingList->getKey(),
            'uuid' => $shoppingList->uuid,
            'period' => $shoppingList->period->value,
            'start_date' => (string) jdate('Y-m-d', $shoppingList->start_date->timestamp),
            'end_date' => (string) jdate('Y-m-d', $shoppingList->end_date->timestamp),
            'message' => match ($shoppingList->status) {
                ShoppingListStatusEnum::PENDING => 'درخواست ساخت سبد خرید در صف پردازش است.',
                ShoppingListStatusEnum::PROCESSING => 'سبد خرید در حال محاسبه است.',
                ShoppingListStatusEnum::READY => 'سبد خرید آماده است.',
                ShoppingListStatusEnum::FAILED => 'ساخت سبد خرید ناموفق بود.',
            },
            'result' => $shoppingList->status === ShoppingListStatusEnum::READY
                ? $shoppingList->result
                : null,
        ];
    }
}
