<?php

namespace Modules\Exercise\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Exercise\Enum\ExercisePlanStrategyDetailTypeEnum;
use Modules\Exercise\Enum\ExerciseSetTypeEnum;

/**
 * Modules\Exercise\Entities\ExercisePlanRequestDetail
 *
 * @property int $id
 * @property int $exercise_plan_request_id
 * @property int|null $exercise_id
 * @property string|null $exercise_name
 * @property int|null $exercise_body_category_id
 * @property int|null $super_set_id
 * @property ExerciseSetTypeEnum $set_type
 * @property ExercisePlanStrategyDetailTypeEnum $type
 * @property int $day
 * @property array|null $reps
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Exercise\Entities\Exercise|null $exercise
 * @property-read \Modules\Exercise\Entities\ExerciseBodyCategory|null $exerciseBodyCategory
 * @property-read \Modules\Exercise\Entities\ExercisePlanRequest $exercisePlanRequest
 * @property-read bool $has_super_set
 * @property-read int $set_count
 * @property-read ExercisePlanRequestDetail|null $superSet
 * @method static \Modules\Exercise\Database\factories\ExercisePlanRequestDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail main()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereExerciseBodyCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereExerciseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereExercisePlanRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereReps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereSetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereSuperSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanRequestDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExercisePlanRequestDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => ExercisePlanStrategyDetailTypeEnum::class,
        'set_type' => ExerciseSetTypeEnum::class,
        'reps' => 'array'
    ];
    protected static function newFactory(): \Modules\Exercise\Database\factories\ExercisePlanRequestDetailFactory
    {
        return \Modules\Exercise\Database\factories\ExercisePlanRequestDetailFactory::new();
    }

    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }

    public function scopeMain($query){
        return $query->whereSetType(ExerciseSetTypeEnum::MAIN_SET);
    }

    public function exercisePlanRequest(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExercisePlanRequest::class, 'exercise_plan_request_id');
    }

    public function exercise(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function exerciseBodyCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExerciseBodyCategory::class, 'exercise_body_category_id');
    }

    public function superSet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExercisePlanRequestDetail::class, 'super_set_id');
    }

    public function getHasSuperSetAttribute(): bool
    {
        return $this->superSet()->exists();
    }

    public function getSetCountAttribute(): int
    {
        return count(array_filter($this->reps, function ($rep) {
            return is_int($rep) &&  $rep > 0;
        }));
    }
}
