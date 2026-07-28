<?php

namespace Modules\Exercise\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Modules\Exercise\Entities\ExerciseBodyCategory
 *
 * @property int $id
 * @property string $name
 * @property int|null $exercise_body_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ExerciseBodyCategory> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\ExercisePlanStrategyDetail> $exercisePlanStrategyDetails
 * @property-read int|null $exercise_plan_strategy_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Exercise\Entities\Exercise> $exercises
 * @property-read int|null $exercises_count
 * @property-read bool $has_children
 * @property-read ExerciseBodyCategory|null $parent
 * @method static \Modules\Exercise\Database\factories\ExerciseBodyCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory main()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory whereExerciseBodyCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseBodyCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExerciseBodyCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function newFactory(): \Modules\Exercise\Database\factories\ExerciseBodyCategoryFactory
    {
        return \Modules\Exercise\Database\factories\ExerciseBodyCategoryFactory::new();
    }

    public function scopeMain($query){
        return $query->whereNull('exercise_body_category_id');
    }


    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'exercise_body_category_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'exercise_body_category_id');
    }

    public function exercises(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercises_body_categories_pivot', 'exercise_body_category_id', 'exercise_id');
    }

    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->exists();
    }

    public function exercisePlanStrategyDetails(): MorphMany
    {
        return $this->morphMany(ExercisePlanStrategyDetail::class, 'exerciseable');
    }
}
