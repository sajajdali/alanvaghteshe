<?php

namespace Modules\Recipe\app\Console;

use Illuminate\Console\Command;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\RecipeKarafs;
use Modules\Recipe\app\Models\Recipe;
use Modules\Recipe\app\Models\RecipeCategory;

class RecipeInsertDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'recipe:insert_db';

    /**
     * The console command description.
     */
    protected $description = 'insert recipe in db from json file.';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $karafsRecipes = RecipeKarafs::all();
        foreach ($karafsRecipes as $recipeKarafs) {
            $thisFood = $recipeKarafs->recipe;
            $categories = $thisFood['category'];
            $recipeUnits = $thisFood['recipe']['food']['foodUnitRatioArray'];


            $category_karafs_id = $categories['_id'];
            $categoryInDB = RecipeCategory::where('detail->karafs->_id', $category_karafs_id)->first();
            if ($categoryInDB){
                $category_id = $categoryInDB->id;
            } else {
                $categoryInDB = RecipeCategory::create([
                    'name' => $categories['name'],
                    'detail' => [
                        'karafs' => $categories,
                    ]
                ]);
                $category_id = $categoryInDB->id;
            }


//           insert recipe
            $foodName = (isset($thisFood['recipe']['newFood']) && $thisFood['recipe']['newFood'] !== '')
                ? $thisFood['recipe']['newFood']
                : $thisFood['recipe']['food']['name'];
            $recipe = Recipe::create([
                'category_id' => $category_id,
                'name' => $foodName,
                'time' => $thisFood['recipe']['time'],
                'serving' => $thisFood['recipe']['serving'],
                'weight' => $thisFood['recipe']['weight'],
                'calorie' => $thisFood['recipe']['food']['foodFact']['calorieAmount'],
                'description' => $thisFood['recipe']['description'] ?? null,
                'difficulty' => $this->changeDifficulty($thisFood['recipe']['difficulty']),
                'foodFact' => $this->castValuesToString($thisFood['recipe']['food']['foodFact']),
                'instructions' => $this->castValuesToString($thisFood['recipe']['instructions']),
                'active' => true,
            ]);


            // categories
            $ingredients = $thisFood['recipe']['ingredients'];
            $pivotData = [];
            foreach ($ingredients as $ingredient) {
                $ingredient_id = $ingredient['food']['id'];
                $ingredient_unit_id = $ingredient['unit']['id'];
                $basicFood = BasicFood::where('detail->karafs->_id', $ingredient_id)->first();
                $unit = FoodUnit::where('detail->_id', $ingredient_unit_id)->first();
                $amount = $ingredient['amount'];
                $pivotData[$basicFood->id] = [
                    'food_unit_id' => $unit->id,

                    'quantity_per_unit' => $amount
                ];
            }
            $recipe->basicFoods()->syncWithoutDetaching($pivotData);


            // recipe units
            $pivotData = [];
            foreach ($recipeUnits as $recipeUnit) {
                $unitId  = $recipeUnit['unitId']['id'];
                $unit = FoodUnit::where('detail->_id', $unitId)->first();
                $pivotData[$unit->id] = [
                    'quantity_per_unit' => $recipeUnit['ratio']
                ];
            }
            $recipe->units()->syncWithoutDetaching($pivotData);
        }
        $this->info('insert is successfully');
    }

    private function changeDifficulty($difficulty): int
    {
        return match ($difficulty) {
            'easy' => 1,
            'normal' => 2,
            default => 3,
        };
    }

    private function castValuesToString($array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->castValuesToString($value); // Recursive call for nested arrays
            } elseif (is_int($value) || is_float($value)) {
                if (is_float($value)) {
                    $value = round($value, 2);
                }
                $value = (string)$value;
            }
        }

        return $array;
    }


}
