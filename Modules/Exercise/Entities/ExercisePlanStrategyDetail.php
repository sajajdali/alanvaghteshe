<?php

namespace Modules\Exercise\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Exercise\Enum\ExercisePlanStrategyDetailTypeEnum;
use Modules\Exercise\Enum\ExerciseSetTypeEnum;

/**
 * Modules\Exercise\Entities\ExercisePlanStrategyDetail
 *
 * @property int $id
 * @property int $exercise_plan_strategy_id
 * @property string $exerciseable_type
 * @property int $exerciseable_id
 * @property int|null $super_set_id
 * @property ExerciseSetTypeEnum $set_type
 * @property ExercisePlanStrategyDetailTypeEnum $type
 * @property int $day
 * @property array|null $reps
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $exerciseable
 * @property-read bool $has_super_set
 * @property-read \Modules\Exercise\Entities\ExercisePlanStrategy $strategy
 * @property-read ExercisePlanStrategyDetail|null $superSet
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail after()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail before()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail copy()
 * @method static \Modules\Exercise\Database\factories\ExercisePlanStrategyDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail main()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereExercisePlanStrategyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereExerciseableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereExerciseableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereReps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereSetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereSuperSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExercisePlanStrategyDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExercisePlanStrategyDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => ExercisePlanStrategyDetailTypeEnum::class,
        'set_type' => ExerciseSetTypeEnum::class,
        'reps' => 'array'
    ];

    protected static function newFactory(): \Modules\Exercise\Database\factories\ExercisePlanStrategyDetailFactory
    {
        return \Modules\Exercise\Database\factories\ExercisePlanStrategyDetailFactory::new();
    }

    public function scopeCopy($query){
        return $query->where('type', ExercisePlanStrategyDetailTypeEnum::COPY);
    }

    public function scopeMain($query){
        return $query->whereSetType(ExerciseSetTypeEnum::MAIN_SET);
    }

    public function scopeBefore($query){
        return $query->where('type', ExercisePlanStrategyDetailTypeEnum::BEFORE);
    }

    public function scopeAfter($query){
        return $query->where('type', ExercisePlanStrategyDetailTypeEnum::AFTER);
    }

    public function strategy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExercisePlanStrategy::class, 'exercise_plan_strategy_id');
    }

    public function exerciseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getHasSuperSetAttribute(): bool
    {
        return $this->superSet()->exists();
    }

    public function superSet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'super_set_id');
    }

    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
}
