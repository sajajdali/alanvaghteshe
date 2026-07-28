<?php

namespace Modules\Recipe\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Recipe\Database\factories\RecipeCategoryFactory;

class RecipeCategory extends Model
{
    use HasFactory;

    protected $casts = [
        'detail' => 'json'
    ];

    protected $table = 'recipe_categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class , 'category_id');
    }
    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }

}
