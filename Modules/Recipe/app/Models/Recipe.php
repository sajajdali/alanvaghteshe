<?php

namespace Modules\Recipe\app\Models;

use Modules\Diet\Entities\BasicFood;
use Illuminate\Database\Eloquent\Model;
use Modules\Api\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Diet\Entities\Food;
use Modules\Recipe\Database\factories\RecipeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static suggested()
 */
class Recipe extends Model
{
    use HasFactory;
    const DETAIL_IMAGE = 'detail_image';
    protected $casts = [
        'foodFact' => 'json',
        'instructions' => 'json',
        'detail' => 'json',
        'suggested' => 'boolean',
        'active' => 'boolean',
        'difficulty' => RecipeDifficultyEnum::class
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function basicFoods(): BelongsToMany
    {
        return $this->belongsToMany(BasicFood::class, 'recipe_basic_foods', 'recipe_id', 'basic_food_id')->withPivot('food_unit_id' , 'quantity_per_unit')->withTimestamps();
    }

    public function scopeSuggested($query)
    {
        return $query->where('suggested', true)->orderBy('number_views')->orderBy('priority');
    }
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RecipeCategory::class , 'category_id');
    }



    public function units():HasMany
    {
        return $this->hasMany(RecipeUnit::class);
    }


    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
    public static function maxOrder(): int
    {
        return self::max('priority') + 1;
    }
    public function getBadgeColor():string {
        return $this->active == true ? 'bg-success' : 'bg-danger' ;
    }
    public function getSuggestedColor():string {
        return $this->suggested == true ? 'bg-success' : 'bg-danger' ;
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
