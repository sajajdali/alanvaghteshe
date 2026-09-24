<?php

namespace Modules\Diet\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Diet\Entities\DietShoppingList;

class ShoppingListStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly DietShoppingList $shoppingList)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->shoppingList->user_id.'.shopping-list'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'shopping-list.'.$this->shoppingList->status->value;
    }

    public function broadcastWith(): array
    {
        return [
            'shopping_list_id' => $this->shoppingList->getKey(),
            'diet_request_id' => $this->shoppingList->diet_request_id,
            'period' => $this->shoppingList->period->value,
            'status' => $this->shoppingList->status->value,
            'message' => match ($this->shoppingList->status->value) {
                'processing' => 'سبد خرید در حال محاسبه است.',
                'ready' => 'سبد خرید شما آماده شد.',
                'failed' => 'ساخت سبد خرید ناموفق بود.',
                default => 'وضعیت سبد خرید تغییر کرد.',
            },
        ];
    }
}
