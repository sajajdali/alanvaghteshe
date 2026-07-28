<?php

namespace Modules\Exercise\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Core\Entities\Disease;
use Modules\Exercise\Enum\ExerciseConditionEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyDetailTypeEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;

/**
 * Modules\Exercise\Entities\Exercise
 *
 * @property int $id
 * @property string $name
 * @property array|null $conditions
 * @property string|null $video
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\ExerciseBodyCategory> $bodyCategories
 * @property-read int|null $body_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Disease> $canDiseases
 * @property-read int|null $can_diseases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Disease> $canNotDiseases
 * @property-read int|null $can_not_diseases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Disease> $diseases
 * @property-read int|null $diseases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\ExercisePlanStrategyDetail> $exercisePlanStrategyDetails
 * @property-read int|null $exercise_plan_strategy_details_count
 * @property-read bool $is_base
 * @property-read bool $is_before_after
 * @property-read bool $is_complementary
 * @property-read bool $is_for_advanced
 * @property-read bool $is_for_beginner
 * @property-read bool $is_for_men
 * @property-read bool $is_for_women
 * @property-read bool $is_main
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Exercise> $subCanExercises
 * @property-read int|null $sub_can_exercises_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Exercise> $subCanNotExercises
 * @property-read int|null $sub_can_not_exercises_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Exercise> $superCanExercises
 * @property-read int|null $super_can_exercises_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Exercise> $superCanNotExercises
 * @property-read int|null $super_can_not_exercises_count
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise advance()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise base()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise beginner()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise complementary()
 * @method static \Modules\Exercise\Database\factories\ExerciseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise gender(\Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum $gender)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise level(\Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum $level)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise main()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise men()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise secondary()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise type(\Modules\Exercise\Enum\ExercisePlanStrategyDetailTypeEnum $typeEnum)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise women()
 * @mixin \Eloquent
 */
class Exercise extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'conditions' => 'array',
    ];

    protected static function newFactory(): \Modules\Exercise\Database\factories\ExerciseFactory
    {
        return \Modules\Exercise\Database\factories\ExerciseFactory::new();
    }

    public function scopeMain($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_MAIN_EXERCISE->value, true);
    }

    public function scopeSecondary($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_BEFORE_AFTER_EXERCISE->value, true);
    }

    public function scopeMen($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_MEN->value, true);
    }

    public function scopeWomen($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_WOMEN->value, true);
    }

    public function scopeGender($query, ExercisePlanStrategyGenderEnum $gender)
    {
        return $gender->is(ExercisePlanStrategyGenderEnum::MALE) ? $query->men() : $query->women();
    }

    public function scopeBeginner($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_BEGINNERS->value, true);
    }

    public function scopeAdvance($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_ADVANCED->value, true);
    }

    public function scopeLevel($query, ExercisePlanStrategyLevelEnum $level)
    {
        return $level->is(ExercisePlanStrategyLevelEnum::BEGINNER) ? $query->beginner() : $query->advance();
    }

    public function scopeBase($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_EXERCISE_BASED->value, true);
    }

    public function scopeComplementary($query)
    {
        return $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_EXERCISE_COMPLEMENTARY->value, true);
    }

    public function scopeType($query, ExercisePlanStrategyDetailTypeEnum $typeEnum)
    {
        return $typeEnum->is(ExercisePlanStrategyDetailTypeEnum::BASE) ? $query->base() : $query->complementary();
    }

    public function getIsForMenAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_FOR_MEN);
    }

    public function getCondition(ExerciseConditionEnum $conditionEnum): bool
    {
        $conditions = $this->conditions;
        if (is_array($conditions) && array_key_exists($conditionEnum->value, $conditions)) {
            return (bool) $conditions[$conditionEnum->value];
        }

        return false;
    }

    public function getIsForWomenAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_FOR_WOMEN);
    }

    public function getIsForBeginnerAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_FOR_BEGINNERS);
    }

    public function getIsForAdvancedAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_FOR_ADVANCED);
    }

    public function getIsBaseAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_EXERCISE_BASED);
    }

    public function getIsComplementaryAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_EXERCISE_COMPLEMENTARY);
    }

    public function getIsMainAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_MAIN_EXERCISE);
    }

    public function getIsBeforeAfterAttribute(): bool
    {
        return $this->getCondition(ExerciseConditionEnum::IS_BEFORE_AFTER_EXERCISE);
    }

    public function bodyCategories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ExerciseBodyCategory::class, 'exercises_body_categories_pivot', 'exercise_id', 'exercise_body_category_id');
    }

    public function superCanExercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercises_superable_pivot', 'exercise_id', 'super_exercise_id')->wherePivot('type', 'can');
    }

    public function superCanNotExercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercises_superable_pivot', 'exercise_id', 'super_exercise_id')->wherePivot('type', 'can_not');
    }

    public function subCanExercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercises_superable_pivot', 'super_exercise_id', 'exercise_id')->wherePivot('type', 'can');
    }

    public function subCanNotExercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercises_superable_pivot', 'super_exercise_id', 'exercise_id')->wherePivot('type', 'can_not');
    }

    public function exercisePlanStrategyDetails(): MorphMany
    {
        return $this->morphMany(ExercisePlanStrategyDetail::class, 'exerciseable');
    }

    public function canDiseases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'diseases_exercises', 'exercise_id', 'disease_id')->wherePivot('type', 'can');
    }

    public function canNotDiseases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'diseases_exercises', 'exercise_id', 'disease_id')->wherePivot('type', 'can_not');
    }

    public function diseases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'diseases_exercises', 'exercise_id', 'disease_id')->withPivot('type');
    }
}
