<?php

namespace Modules\Diet\Entities;

use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Diet\app\Events\DietRequestAdded;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modules\Diet\Entities\DietRequest
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $diet_plan_id
 * @property DietRequestStatusEnum $status
 * @property int $calories
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int $active
 * @property mixed $user_information
 * @property mixed $detail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Condition> $conditions
 * @property-read int|null $conditions_count
 * @property-read \Modules\Diet\Entities\DietPlan|null $dietPlan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietRequestDetail> $dietRequestDetails
 * @property-read int|null $diet_request_details_count
 * @property-read User|null $user
 * @method static \Modules\Diet\Database\factories\DietRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereDietPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest whereUserInformation($value)
 * @property-read int $json_encode_options_for_detail
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequest withoutTrashed()
 * @method static Builder<static>|DietRequest successfulDiet()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietShoppingList> $shoppingLists
 * @property-read int|null $shopping_lists_count
 * @mixin \Eloquent
 */
class DietRequest extends Model
{
    use HasFactory, SoftDeletes;

    const ACTION_REQUEST_INSERT = 1;
    const ACTION_REQUEST_RE_GENERATE = 2;
    const ACTION_REQUEST_UPDATE_LIST = 3;

    const KEY_DETAIL_REPORT_MAKE_DIET = 'report_diet';
    const KEY_DETAIL_HANDWRITTEN = 'handwritten';
    const KEY_DETAIL_SPECIAL_BASIC_FOODS = 'special_basic_foods';
    const KEY_DETAIL_TRAINING_DAYS = 'training_days';
    protected $guarded = ['id'];

    protected $casts = [
        'status' => DietRequestStatusEnum::class,
        'detail' => 'json',
        'user_information'  => 'json',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
    protected $with = ['dietRequestDetails'];

    protected static function newFactory(): \Modules\Diet\Database\factories\DietRequestFactory
    {
        return \Modules\Diet\Database\factories\DietRequestFactory::new();
    }

    protected static function booted()
    {
        // Handle the `created` event
        static::created(function ($dietRequest) {
            if ($dietRequest->status == 30) {
                event(new DietRequestAdded($dietRequest));
            }
        });

        // Handle the `updated` event
        static::updated(function ($dietRequest) {
            if ($dietRequest->isDirty('status')) {
                event(new DietRequestAdded($dietRequest));
            }
        });
    }

    #[Scope]
    protected function successfulDiet(Builder $query) {
        $query->whereIn('status',[DietRequestStatusEnum::ACTIVE,DietRequestStatusEnum::END]) ;
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dietPlan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }

    public function conditions(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Condition::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    public function dietRequestDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DietRequestDetail::class);
    }

    public function shoppingLists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DietShoppingList::class);
    }

    public function getJsonEncodeOptions(): int
    {
        $options = parent::getJsonEncodeOptions();

        // Check if the 'detail' attribute is being encoded and apply options if true
        if ($this->isCastingAttribute('detail')) {
            $options |= $this->getJsonEncodeOptionsForDetailAttribute();
        }

        return $options;
    }
    protected function getJsonEncodeOptionsForDetailAttribute(): int
    {
        return JSON_PRESERVE_ZERO_FRACTION;
    }

    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }

    public function activeBadge() :string
    {
       return match($this->active)
         {
            0 => ' <span class=" text-white bg-danger p-2 rounded-pill" >غیرفعال</span>' ,
            1 => ' <span class=" text-white bg-success p-2 rounded-pill" >فعال</span>',
            default => '-'
        };
    }

}
