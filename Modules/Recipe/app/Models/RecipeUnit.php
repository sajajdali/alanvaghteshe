<?php

namespace Modules\Recipe\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Recipe\Database\factories\RecipeUnitFactory;

class RecipeUnit extends Model
{
    use HasFactory;

    protected $table = 'recipe_units';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

}
