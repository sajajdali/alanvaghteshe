<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Diet\Enum\ShoppingListPeriodEnum;
use Modules\Diet\Enum\ShoppingListStatusEnum;
use Modules\User\Entities\User;

class DietShoppingList extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'period' => ShoppingListPeriodEnum::class,
        'status' => ShoppingListStatusEnum::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'result' => 'array',
        'started_at' => 'datetime',
        'generated_at' => 'datetime',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dietRequest(): BelongsTo
    {
        return $this->belongsTo(DietRequest::class);
    }
}
