<?php

namespace Modules\Diet\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Diet\Entities\DietShoppingList;
use Modules\Diet\Enum\ShoppingListStatusEnum;
use RuntimeException;

class DietShoppingListResultManager
{
    public function setChecked(DietShoppingList $shoppingList, string $itemKey, bool $isChecked): DietShoppingList
    {
        return DB::transaction(function () use ($shoppingList, $itemKey, $isChecked): DietShoppingList {
            $lockedList = DietShoppingList::query()->lockForUpdate()->findOrFail($shoppingList->getKey());

            if ($lockedList->status !== ShoppingListStatusEnum::READY || !is_array($lockedList->result)) {
                throw new RuntimeException('Shopping list is not ready.');
            }

            $result = $lockedList->result;
            $found = false;
            $totalItems = 0;
            $checkedItems = 0;

            foreach ($result['groups'] as &$group) {
                foreach ($group['items'] as &$item) {
                    if (($item['key'] ?? null) === $itemKey) {
                        $item['is_checked'] = $isChecked;
                        $found = true;
                    }

                    $totalItems++;
                    if ((bool) ($item['is_checked'] ?? false)) {
                        $checkedItems++;
                    }
                }
            }

            if (!$found) {
                throw ValidationException::withMessages([
                    'item_key' => 'قلم موردنظر در سبد خرید وجود ندارد.',
                ]);
            }

            $result['total_items'] = $totalItems;
            $result['checked_items'] = $checkedItems;
            $result['progress_percent'] = $totalItems > 0
                ? (int) round(($checkedItems / $totalItems) * 100)
                : 0;

            $lockedList->forceFill(['result' => $result])->save();

            return $lockedList->refresh();
        });
    }
}
