<?php

use Modules\Diet\Livewire\Admin\Food\FoodCreateOrUpdate;
use Modules\Diet\Livewire\Admin\Meal\MealCreateOrUpdate;
use Modules\Diet\Livewire\Admin\Plan\DietPlanCreateOrUpdate;
use Modules\Diet\Livewire\Admin\Recipe\RecipeUpdateOrCreate;
use Modules\Diet\Livewire\Admin\FoodUnit\FoodUnitCreateOrUpdate;
use Modules\Diet\Livewire\Admin\BasicFood\BasicFoodCreateOrUpdate;
use Modules\Diet\Livewire\Admin\Condition\ConditionCreateOrUpdate;
use Modules\Diet\Livewire\Admin\Recipe\RecipeList;

Route::get('basic_food', \Modules\Diet\Livewire\Admin\BasicFood\BasicFoodList::class)->name('basic_food.index')->can('viewAny', \Modules\Diet\Entities\BasicFood::class);
Route::get('basic_food/create', BasicFoodCreateOrUpdate::class)->name('basic_food.create')->can('create', \Modules\Diet\Entities\BasicFood::class);
Route::get('basic_food/edit/{basic_food}', BasicFoodCreateOrUpdate::class)->name('basic_food.edit');

Route::get('meal', \Modules\Diet\Livewire\Admin\Meal\MealList::class)->name('meal.index')->can('viewAny', \Modules\Diet\Entities\Meal::class);
Route::get('meal/create', MealCreateOrUpdate::class)->name('meal.create')->can('create', \Modules\Diet\Entities\Meal::class);
Route::get('meal/edit/{meal}', MealCreateOrUpdate::class)->name('meal.edit');

Route::get('nutrition_tip', \Modules\Diet\Livewire\Admin\NutritionTrip\NutritionTipList::class)->name('nutrition_tip.index')->can('viewAny', \Modules\Diet\app\Models\NutritionTip::class);
Route::get('nutrition_tip/create', \Modules\Diet\Livewire\Admin\NutritionTrip\NutritionTipCreateOrUpdate::class)->name('nutrition_tip.create')->can('create', \Modules\Diet\app\Models\NutritionTip::class);
Route::get('nutrition_tip/edit/{nutritionTip}', \Modules\Diet\Livewire\Admin\NutritionTrip\NutritionTipCreateOrUpdate::class)->name('nutrition_tip.edit');


Route::get('food_unit', \Modules\Diet\Livewire\Admin\FoodUnit\FoodUnitList::class)->name('food_unit.index')->can('viewAny', \Modules\Diet\Entities\FoodUnit::class);
Route::get('food_unit/create', FoodUnitCreateOrUpdate::class)->name('food_unit.create')->can('create', \Modules\Diet\Entities\FoodUnit::class);
Route::get('food_unit/edit/{food_unit}', FoodUnitCreateOrUpdate::class)->name('food_unit.edit');

Route::get('food', \Modules\Diet\Livewire\Admin\Food\FoodList::class)->name('food.index')->can('viewAny', \Modules\Diet\Entities\Food::class);
Route::get('food/create', FoodCreateOrUpdate::class)->name('food.create')->can('create', \Modules\Diet\Entities\Food::class);
Route::get('food/edit/{food}', FoodCreateOrUpdate::class)->name('food.edit');

Route::get('condition', \Modules\Diet\Livewire\Admin\Condition\ConditionList::class)->name('condition.index')->can('viewAny', \Modules\Diet\Entities\Condition::class);
Route::get('condition/create', ConditionCreateOrUpdate::class)->name('condition.create')->can('create', \Modules\Diet\Entities\Condition::class);
Route::get('condition/edit/{condition}', ConditionCreateOrUpdate::class)->name('condition.edit');

Route::get('diet_plan', \Modules\Diet\Livewire\Admin\Plan\DietPlanList::class)->name('diet_plan.index')->can('viewAny', \Modules\Diet\Entities\DietPlan::class);
Route::get('diet_plan/create', DietPlanCreateOrUpdate::class)->name('diet_plan.create')->can('create', \Modules\Diet\Entities\DietPlan::class);
Route::get('diet_plan/edit/{diet_plan}', DietPlanCreateOrUpdate::class)->name('diet_plan.edit');

Route::get('recipe/create', RecipeUpdateOrCreate::class)->name('recipe.create');
Route::get('recipe/list', RecipeList::class)->name('recipe.list');
Route::get('recipe/edit/{recipe}', RecipeUpdateOrCreate::class)->name('recipe.edit');
//Route::get('requets_diet' , )

Route::get('/rejim' , function (){
    $rejim = new \Modules\Diet\Helpers\Rejim();
    dd($rejim->makeRejim());
});
