<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Diet\Enum\ShoppingListPeriodEnum;
use Modules\Diet\Enum\ShoppingListStatusEnum;
use Modules\User\Entities\User;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $diet_request_id
 * @property ShoppingListPeriodEnum $period
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property ShoppingListStatusEnum $status
 * @property string $input_hash
 * @property array<array-key, mixed>|null $result
 * @property string|null $model
 * @property int|null $input_tokens
 * @property int|null $output_tokens
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $generated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Diet\Entities\DietRequest $dietRequest
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereDietRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereInputHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereInputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereOutputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DietShoppingList whereUuid($value)
 * @mixin \Eloquent
 */
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
