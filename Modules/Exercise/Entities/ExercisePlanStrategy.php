<?php

namespace Modules\Exercise\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum;
use Modules\Exercise\Jobs\MakeExercisePlanJob;

/**
 * Modules\Exercise\Entities\ExercisePlanStrategy
 *
 * @property int $id
 * @property string $name
 * @property int $session_count
 * @property ExercisePlanStrategyTargetEnum $target
 * @property ExercisePlanStrategyGenderEnum $gender
 * @property ExercisePlanStrategyLevelEnum $level
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\ExercisePlanStrategyDetail> $details
 * @property-read int|null $details_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy active()
 * @method static \Modules\Exercise\Database\factories\ExercisePlanStrategyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereSessionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExercisePlanStrategy extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'target' => ExercisePlanStrategyTargetEnum::class,
        'gender' => ExercisePlanStrategyGenderEnum::class,
        'level' => ExercisePlanStrategyLevelEnum::class,
        'status' => 'boolean'
    ];
    protected static function newFactory(): \Modules\Exercise\Database\factories\ExercisePlanStrategyFactory
    {
        return \Modules\Exercise\Database\factories\ExercisePlanStrategyFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExercisePlanStrategyDetail::class, 'exercise_plan_strategy_id');
    }

    public function makeRequest(?int $user_id= null) : ?int{
        //make ExercisePlanRequest from ExercisePlanStrategy
        $exercisePlan = ExercisePlanRequest::create([
            'user_id' => $user_id,
            'exercise_plan_strategy_id' => $this->id,
            'session_count' => $this->session_count,
            'target' => $this->target,
            'gender' => $this->gender,
            'level' => $this->level,
            'status' => ExercisePlanRequestEnum::COMPUTING,
        ]);
        MakeExercisePlanJob::dispatch($exercisePlan->id);
        return $exercisePlan->id;
    }
}
