<?php

namespace Modules\Exercise\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum;
use Modules\Exercise\Jobs\MakeExercisePlanJob;
use Modules\Setting\Interface\ModelHasSettingOptionInterface;
use Modules\User\Entities\User;

/**
 * Modules\Exercise\Entities\ExercisePlanRequest
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $exercise_plan_strategy_id
 * @property int $session_count
 * @property ExercisePlanStrategyTargetEnum $target
 * @property ExercisePlanStrategyGenderEnum $gender
 * @property ExercisePlanStrategyLevelEnum $level
 * @property ExercisePlanRequestEnum $status
 * @property \Illuminate\Support\Carbon|null $start_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\ExercisePlanRequestDetail> $details
 * @property-read int|null $details_count
 * @property-read \Modules\Exercise\Entities\ExercisePlanStrategy|null $exercisePlanStrategy
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest active()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest complete()
 * @method static \Modules\Exercise\Database\factories\ExercisePlanRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereExercisePlanStrategyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereSessionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereUserId($value)
 * @property array|null $detail
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequest whereDetail($value)
 * @mixin \Eloquent
 */
class ExercisePlanRequest extends Model implements ModelHasSettingOptionInterface
{
    use HasFactory;


    protected $guarded = ['id'];

    protected $casts = [
        'target' => ExercisePlanStrategyTargetEnum::class,
        'gender' => ExercisePlanStrategyGenderEnum::class,
        'level' => ExercisePlanStrategyLevelEnum::class,
        'status' => ExercisePlanRequestEnum::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'detail' => 'json',
    ];

    public static function getArrayForSetting(mixed $type = null): array
    {
        return ExercisePlanRequestEnum::toArray();
    }

    protected static function newFactory(): \Modules\Exercise\Database\factories\ExercisePlanRequestFactory
    {
        return \Modules\Exercise\Database\factories\ExercisePlanRequestFactory::new();
    }

    public function scopeComplete($query)
    {
        return $query->where('status', ExercisePlanRequestEnum::COMPLETED);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ExercisePlanRequestEnum::COMPLETED)->orWhere('status',
            ExercisePlanRequestEnum::ENDED)->orWhere('status',
            ExercisePlanRequestEnum::FAILED);
    }

    public function exercisePlanStrategy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExercisePlanStrategy::class, 'exercise_plan_strategy_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExercisePlanRequestDetail::class, 'exercise_plan_request_id');
    }

    public function rePlan(): void
    {
        $this->update(['status' => ExercisePlanRequestEnum::COMPUTING]);
        //dispatch MakeExercisePlanJob job
        MakeExercisePlanJob::dispatch($this->id);
    }
}
