<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Diet\Entities\RecipeKarafs
 *
 * @property int $id
 * @property string $uuid
 * @property array|null $recipe
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecipeKarafs whereUuid($value)
 * @mixin \Eloquent
 */
class RecipeKarafs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    protected $table = 'recipe_karafs';
    protected $casts = [
        'recipe' => 'json',
    ];

}
