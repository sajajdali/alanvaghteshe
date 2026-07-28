<?php

namespace Modules\Diet\Traits;

use Carbon\Carbon;
use Modules\Api\app\Resources\RequestDietDetailResourcev2;
use Modules\Api\Enum\PopupEnum;
use Modules\Core\Entities\Disease;
use Modules\Diet\app\Jobs\CreateJsonDietJob;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Diet\Service\DietService;
use Verta;

trait MakeDietV2
{
    /**
     * Generates a diet plan based on the user's request and available foods.
     *
     * @param DietRequest $dietRequest The diet request containing user preferences and calorie requirements.
     * @return array Returns the result of saving the generated diet plan details.
     */
    public function makeDietFromFoods(DietRequest $dietRequest): array
    {
        // Extract the total daily calorie requirement from the diet request details.
        $totalCalories = $dietRequest->calories;

        // Group foods by meal type (e.g., breakfast, lunch, dinner) based on the calorie requirement.
        $foodsGroupedByMeal = $this->getFoodsGroupedByMeal($dietRequest, $totalCalories);

        // Initialize the list of selected foods for each meal type.
        $selectedFoodsPerMeal = $this->initializeSelectedFoods($foodsGroupedByMeal);

        // Determine the number of days in the diet plan.
        $days = $dietRequest->dietPlan->day_count;
        $result = []; // Initialize the result array to store daily meal plans.

        // Generate a daily meal plan for each day in the diet plan.
        for ($day = 1; $day <= $days; $day++) {
            $result[$day] = $this->generateDailyPlan($day, $dietRequest, $foodsGroupedByMeal, $selectedFoodsPerMeal);
        }

        // Prepare the final result structure, including a calorie breakdown and daily plans.
        $result = [
            'data' => [
                'calorieBreakdown' => [
                    'total' => $totalCalories, // Total daily calories required.
                    'meals' => $this->calculateMealCalories($dietRequest->dietPlan, $totalCalories), // Calories allocated per meal.
                ],
            ],
            'result' => $result, // The generated daily meal plans.
        ];

        // Process the generated food list to finalize the meal plan.
        $userDiet = $this->processFoodList($result);

        $detail = $dietRequest->detail;
        $detail[DietRequest::KEY_DETAIL_REPORT_MAKE_DIET] = formatDecimalArray($userDiet);
        $dietRequest->update([
            'detail' => $detail
        ]);

        // Save the processed meal plan details with a snapshot of the selected foods.
        $this->saveDietRequestDetailsWithSnapshot($dietRequest, $userDiet);

        return [
            'data' => $userDiet
        ];
    }

    public function replaceDietRequestDetail(DietRequestDetail $currentDietRequestDetail, $newFoodId)
    {
        // واکشی اطلاعات مربوط به غذای جدید
        $newFood = Food::with('basicFoodPivot')->find($newFoodId);
        if (!$newFood) {
            throw new \Exception('غذای موردنظر یافت نشد.');
        }

        $dietRequest = $currentDietRequestDetail->dietRequest;
        $meal = $currentDietRequestDetail->meal;

        $parentFoodId = $newFood->id;

        while (!is_null($currentDietRequestDetail->replaced_parent_id)) {
            $originalDietRequestDetail = DietRequestDetail::find($currentDietRequestDetail->replaced_parent_id);
            if (!$currentDietRequestDetail) {
                throw new \Exception("Original DietRequestDetail not found.");
            }
        }
        $requiredCalories = $currentDietRequestDetail->calories;


        $relatedFoods = Food::where('parent_id', $parentFoodId)->get();

        $closestFood = $relatedFoods->filter(function ($food) use ($requiredCalories) {
            return !is_null($food->calories) && $food->calories < $requiredCalories;
        })->sortBy(function ($food) use ($requiredCalories) {
            return abs($food->calories - $requiredCalories);
        })->first();


        if (!$closestFood) {
            throw new \Exception('غذای مناسب با کالری موردنیاز پیدا نشد.');
        }

        $basicFoods = $closestFood->basicFoodPivot->map(function ($pivot) use ($meal) {

            $foodUnit = $pivot->units()->where('is_primary', '1')->first() ?? $pivot->units()->first();
            $quantityPerUnit = $foodUnit->pivot->quantity_per_unit;
            $caloriePer100Gram = (int)($pivot->food_fact['CALORIE'] ?? 0);
            $proteinPer100Gram = (int)($pivot->food_fact['PROTEIN'] ?? 0);
            $fatPer100Gram = (int)($pivot->food_fact['fat'] ?? 0);
            $fiberPer100Gram = (int)($pivot->food_fact['fiber'] ?? 0);
            $carbohydratePer100Gram = (int)($pivot->food_fact['CARBOHYDRATE'] ?? 0);
            $calorie = 0;
            if ($caloriePer100Gram > 0) {
                $calorie = ($caloriePer100Gram * $quantityPerUnit) / 100;
            }
            return [
                'basic_food_id' => $pivot->id, // Basic food ID
                'name' => $pivot->name, // Name of the basic food
                'quantity' => $pivot->pivot->quantity, // Quantity required for this food
                'weight_per_gram' => round($quantityPerUnit * $pivot->pivot->quantity),
                'calorie' => round($calorie * $pivot->pivot->quantity),
                'fat' => round($fatPer100Gram * $pivot->pivot->quantity),
                'protein' => round($proteinPer100Gram * $pivot->pivot->quantity),
                'fiber' => round($fiberPer100Gram * $pivot->pivot->quantity),
                'carb' => round($carbohydratePer100Gram * $pivot->pivot->quantity),
                'unit' => $foodUnit->name, // Fallback to the first unit if no primary exists
                'unit_id' => $foodUnit->id,
            ];
        });
        // محاسبه اطلاعات غذایی جدید


        // ساخت snapshot غذا
        $foodSnapshot = [
            'food_id' => $closestFood->id,
            'food_name' => $closestFood->name,
            'basic_foods' => $basicFoods,
        ];

        // ساخت داده‌های جدید برای ذخیره در دیتابیس
        $newDietRequestDetailData = [
            'diet_request_id' => $dietRequest->id,
            'meal_id' => $meal->id,
            'carb' => $closestFood->carb,
            'protein' => $closestFood->protein,
            'fat' => $closestFood->fat,
            'fiber' => $closestFood->fiber,
            'calories' => $closestFood->calories,
            'day_number' => $currentDietRequestDetail->day_number,
            'date_of_day' => $currentDietRequestDetail->date_of_day,
            'detail' => json_encode(formatDecimalArray([
                'calorie_difference' => '',
                'food_snapshot' => $foodSnapshot,
            ]), JSON_UNESCAPED_UNICODE),

        ];

        // ذخیره رکورد جدید
        $newDietRequestDetail = Food::find($closestFood->parent_id)->dietRequestDetails()->create($newDietRequestDetailData);

        // به‌روزرسانی رکورد فعلی با parent_id
        $currentDietRequestDetail->update([
            'replaced_parent_id' => $newDietRequestDetail->id,
        ]);

        // به‌روزرسانی JSON رژیم
//        CreateJsonDietJob::dispatch($dietRequest)->onQueue('low');
        dispatch_sync(new CreateJsonDietJob($dietRequest));

        return [
            'status' => true,
            'message' => 'غذای جایگزین با موفقیت انجام شد.',
            'new_diet_request_detail_id' => $newDietRequestDetail->id,
            'meal' => $this->getOneMeal($currentDietRequestDetail->dietRequest->user, $meal, Carbon::make($currentDietRequestDetail->date_of_day))
        ];
    }

    public function handleConditions($conditions)
    {
        if (count($conditions) > 0) {
            if (array_key_exists(ConditionKeyEnum::FOOD_RESTRICTION->value, $conditions)) {
                $eatsEverything = 3;

                //unset food restrictions when user is omnivore
                if (in_array($eatsEverything, $conditions[ConditionKeyEnum::FOOD_RESTRICTION->value])) {
                    unset($conditions[ConditionKeyEnum::FOOD_RESTRICTION->value]);
                }
            }
        }

        return $conditions;
    }

    public function replaceMealListFoodsV2(DietRequestDetail $dietRequestDetail)
    {
        // Calculate total daily calories from the diet request
        $totalCalories = $dietRequestDetail->dietRequest->calories;

        // Calculate calories allocated for each meal
        $totalCaloriePerMeals = collect($this->calculateMealCalories($dietRequestDetail->dietRequest->dietPlan, $totalCalories));

        // Extract user conditions from the diet request
        $conditions = $this->handleConditions($dietRequestDetail->dietRequest->detail['condition']);

        // Get the meal associated with the current diet request detail
        $meal = $dietRequestDetail->meal;

        // Calculate the calorie requirement for this specific meal
//        $mealCalorie = $totalCaloriePerMeals->firstWhere('meal_id', $meal->id)['calories'] ?? 0;

        $originalDietRequestDetail = $dietRequestDetail;

        while (!is_null($originalDietRequestDetail->replaced_parent_id)) {
            $originalDietRequestDetail = DietRequestDetail::find($originalDietRequestDetail->replaced_parent_id);
            if (!$originalDietRequestDetail) {
                throw new \Exception("Original DietRequestDetail not found.");
            }
        }
        $mealCalorie = $originalDietRequestDetail->calories;

        // Check if this is a special meal
        if ($this->isSpecialMeal($dietRequestDetail->dietRequest, $meal)) {
            $specialMealsId = $dietRequestDetail->dietRequest->dietPlan->detail[DietPlan::DETAIL_SPECIAL_BASIC_FOODS][$meal->id];

            // If multiple special foods exist, remove the current one to avoid repetition
            if (count($specialMealsId) > 1) {
                unset($specialMealsId[$dietRequestDetail->id]);
            }

            // Return the list of special foods
            return Food::whereIn('id', $specialMealsId)->get();
        }

        // If not a special meal, retrieve the list of allowable foods based on conditions
        $listFoods = self::getFoods($conditions, $meal->id);

        // Filter foods that match the calorie range of the meal
        $filteredFoods = $listFoods->filter(function ($food) use ($mealCalorie) {
            // Exclude foods with null calorie values
            if (is_null($food->calories) || is_null($food->max_calories)) {
                return false;
            }

            if ((float)$food->calories > (float)$mealCalorie || (float)$mealCalorie > (float)$food->max_calories) {
                return false;
            }

            // Check if the food has at least one child with calories <= mealCalorie
            return Food::where('parent_id', $food->id)
                ->where('calories', '<=', $mealCalorie)
                ->exists();

        });


        // Return the filtered list if not empty
        if ($filteredFoods->isNotEmpty()) {
            return $filteredFoods;
        }

        // If no matching foods found, return an empty collection
        return collect([]);
    }
    public function getOneMeal($user, $meal, $date)
    {

        $dietDetails = $user->activeDiet()
            ?->dietRequestDetails()
            ?->where('meal_id', $meal->id)
            ?->whereNull('replaced_parent_id')
            ?->where('date_of_day', $date->toDateString())
            ?->whereHas('dietRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ?->get() ?? collect();

//        $dietDetails = DietRequestDetail::where('meal_id', $meal->id)
//            ->whereNull('replaced_parent_id')
//            ->where('date_of_day', $date->toDateString())
//            ->whereHas('dietRequest', function ($query) use ($user) {
//                $query->where('user_id', $user->id);
//            })
//            ->get();

        if ($dietDetails->isNotEmpty()) {
            $recipe_id = null;
            $dietDetail = $dietDetails->first();
            if ($dietDetail->foodable->recipes()->count()) {
                $recipe_id = $dietDetail->foodable->recipes->first()->id;
            }
            $dateOfDay = Carbon::parse($dietDetail->date_of_day);
            $dateOfDay->setTime(23, 59, 59);
            $isToday = $dateOfDay->isToday();
            $isFuture = $dateOfDay->isFuture();

            // change meal name
            $getMealName = app(DietService::class)->handleMealName($dietDetail->dietRequest , $meal);
            return [
                'title' => $getMealName,
                'icon_url' => $meal->icon_url ?? url('assets/admin/images/food_icon.png'),
                'is_done' => $dietDetail->is_done,
                'cheat_meal' => $dietDetail->cheat_meal,
                'priority' => 1,
                'hand_written' => null,
                'meal_id' => $dietDetail->meal_id,
                'diet_request_id' => $dietDetail->id,
                'recipe_id' => $recipe_id,
                'meals' => RequestDietDetailResourcev2::collection($dietDetails)->resolve()['0']['meals'],
                'consumed_foods' => [],
                'meal_recipe' => null,
                'meal_calories' => $dietDetail->calories,
                'accessibility' => [
                    'is_today' => $isToday,
                    'add_food' => $isToday || !$isFuture,
                    'active_day' => $isFuture || $isToday,
                    'active_consumed_meal' => ($isToday || !$isFuture) && !$dietDetail->is_done,
                ]
            ];
        }
        return null;
    }

    /**
     * Save diet request details along with a snapshot of the selected food information.
     *
     * @param DietRequest $dietRequest The diet request object containing user preferences.
     * @param array $processedData The processed diet data with details for each day and meal.
     * @return bool Returns true upon successful saving of the data.
     */
    private function saveDietRequestDetailsWithSnapshot(DietRequest $dietRequest, array $processedData)
    {
        // Iterate through the processed data for each day
        foreach ($processedData['result'] as $day => $meals) {
            // Iterate through each meal for the given day
            foreach ($meals as $meal) {

                /// Skip meals where either final_food_id or food_id is not set
                if ($meal['final_food_id'] == null || $meal['food_id'] == null) {
                    continue;
                }

                // Retrieve the food along with its related basic food pivot information
                $food = Food::with('basicFoodPivot')->find($meal['final_food_id']);
                if (!$food) {
                    // Skip if no food is found for the final_food_id
                    continue;
                }

                // Map basic food details from the food's basic food pivot relationship
                $basicFoods = $food->basicFoodPivot->map(function ($pivot) use ($meal) {

                    $foodUnit = $pivot->units()->where('is_primary', '1')->first() ?? $pivot->units()->first();
                    $quantityPerUnit = $foodUnit->pivot->quantity_per_unit;
                    $caloriePer100Gram = (int)($pivot->food_fact['CALORIE'] ?? 0);
                    $proteinPer100Gram = (int)($pivot->food_fact['PROTEIN'] ?? 0);
                    $fatPer100Gram = (int)($pivot->food_fact['fat'] ?? 0);
                    $fiberPer100Gram = (int)($pivot->food_fact['fiber'] ?? 0);
                    $carbohydratePer100Gram = (int)($pivot->food_fact['CARBOHYDRATE'] ?? 0);
                    $calorie = 0;
                    if ($caloriePer100Gram > 0) {
                        $calorie = ($caloriePer100Gram * $quantityPerUnit) / 100;
                    }
                    return [
                        'basic_food_id' => $pivot->id, // Basic food ID
                        'name' => $pivot->name, // Name of the basic food
                        'quantity' => $pivot->pivot->quantity, // Quantity required for this food
                        'weight_per_gram' => round($quantityPerUnit * $pivot->pivot->quantity),
                        'calorie' => round($calorie * $pivot->pivot->quantity),
                        'fat' => round($fatPer100Gram * $pivot->pivot->quantity),
                        'protein' => round($proteinPer100Gram * $pivot->pivot->quantity),
                        'fiber' => round($fiberPer100Gram * $pivot->pivot->quantity),
                        'carb' => round($carbohydratePer100Gram * $pivot->pivot->quantity),
                        'unit' => $foodUnit->name, // Fallback to the first unit if no primary exists
                        'unit_id' => $foodUnit->id,
                    ];
                });

                // Create a snapshot of the food and its associated basic foods
                $foodSnapshot = [
                    'food_id' => $food->id, // ID of the selected food
                    'food_name' => $food->name, // Name of the selected food
                    'basic_foods' => $basicFoods, // List of basic foods associated with the selected food
                ];

                // Prepare the data to save into the diet_request_details table
                $dietDay = $day;
                $hour = Carbon::now()->hour;
                if ($hour >= 0 && $hour < 12) {
                    $dietDay = $day - 1;
                }
                $dietRequestDetailData = [
                    'diet_request_id' => $dietRequest->id,
                    'meal_id' => $meal['meal_id'],
                    'carb' => $food->carb,
                    'protein' => $food->protein,
                    'fat' => $food->fat,
                    'fiber' => $food->fiber,
                    'calories' => $meal['final_calories'],
                    'day_number' => $day,
                    'date_of_day' => Carbon::now()->addDays($dietDay)->toDateString(),
                    'detail' => json_encode(formatDecimalArray([
                        'calorie_difference' => $meal['calorie_difference'],
                        'food_snapshot' => $foodSnapshot,
                    ]), JSON_UNESCAPED_UNICODE),

                ];


                // Save the diet request detail record in the database
                $food = Food::find($meal['food_id'])->dietRequestDetails()->create($dietRequestDetailData);
            }
        }
        return true;
    }

    /**
     * Calculates the calorie allocation for each meal based on the total daily calories and the meal's calorie percentage.
     *
     * @param DietPlan $dietPlan The diet plan object containing meal details and calorie percentages.
     * @param float $totalCalories The total daily calorie requirement.
     * @return array Returns an array of calorie details for each meal.
     */
    private function calculateMealCalories($dietPlan, $totalCalories)
    {
        $mealCalorieDetails = []; // Initialize an array to store calorie details for each meal.

        // Iterate through each meal in the diet plan.
        foreach ($dietPlan->meals as $meal) {
            // Calculate the calories for the current meal based on its percentage of the total calories.
            $mealCalories = round($totalCalories * ($meal->pivot->calorie_percent / 100), 1);

            // Add the calculated calorie details for the meal for the result array.
            $mealCalorieDetails[] = [
                'meal_id' => $meal->id,
                'meal_name' => $meal->name,
                'calories' => $mealCalories,
            ];
        }

        return $mealCalorieDetails; // Return the array containing calorie details for all meals.
    }

    private function haveSpecialMeal(DietRequest $dietRequest, $mealId): bool
    {
        if (array_key_exists($mealId, $dietRequest->dietPlan->detail[DietPlan::DETAIL_SPECIAL_BASIC_FOODS])) {
            return true;
        }
        return false;
    }

    /**
     * Groups foods by meal type based on calorie requirements and user-specific conditions.
     *
     * @param DietRequest $dietRequest The diet request containing user preferences and conditions.
     * @param float $totalCalories The total daily calorie requirement.
     * @return array An array of foods grouped by meal type.
     */
    private function getFoodsGroupedByMeal(DietRequest $dietRequest, $totalCalories)
    {
        // Retrieve user-specific conditions or use default conditions if not available.
        $conditions = $dietRequest->detail['condition'] ?? app('dietService')->getUserConditions($dietRequest->user);
        $conditions = $this->handleConditions($conditions);

        // Calculate the calorie requirements for each meal.
        $mealCaloriesDetails = $this->calculateMealCalories($dietRequest->dietPlan, $totalCalories);
        $foodsGroupedByMeal = []; // Initialize an array to store foods grouped by meal.

        // Iterate over each meal and its calorie details.
        foreach ($mealCaloriesDetails as $mealDetails) {
            $mealId = $mealDetails['meal_id'];
            $mealCalories = $mealDetails['calories'];

            // Check if the meal has special foods defined in the diet plan.
            if (self::haveSpecialMeal($dietRequest, $mealId)) {
                $specialMealsId = $dietRequest->dietPlan->detail[DietPlan::DETAIL_SPECIAL_BASIC_FOODS][$mealId];
                $filteredFoods = Food::whereIn('id', $specialMealsId);
            } else {
                // Retrieve the list of foods based on conditions and meal type.
                $listFoods = self::getFoods($conditions, $mealId);

                // Filter foods that fit within the calorie range for the meal.
                $filteredFoods = $listFoods->filter(function ($food) use ($mealCalories) {
                    return $food->calories <= $mealCalories && $food->max_calories >= $mealCalories;
                });
            }

            // Store the IDs of the filtered foods for this meal.
            $foodsGroupedByMeal[$mealId] = $filteredFoods->pluck('id')->toArray();
        }

        return $foodsGroupedByMeal;  // Return the grouped foods by meal type.
    }

    /**
     * Initializes the list of selected foods for each meal.
     *
     * @param array $foodsGroupedByMeal An array of foods grouped by meal type.
     * @return array Returns an array of initialized selected foods for each meal.
     */
    private function initializeSelectedFoods($foodsGroupedByMeal)
    {
        $selectedFoodsPerMeal = [];  // Initialize an empty array to store selected foods for each meal.

        // Iterate through the grouped foods by meal.
        foreach ($foodsGroupedByMeal as $mealId => $foods) {
            // Assign the list of foods for the current meal ID to the selected foods array.
            $selectedFoodsPerMeal[$mealId] = $foods;
        }

        // Return the initialized array of selected foods for all meals.
        return $selectedFoodsPerMeal;
    }

    /**
     * Processes the food list for each day and meal, ensuring the closest calorie match and adjusting calorie offsets.
     *
     * @param array $data The input data containing calorie breakdown and initial meal list.
     * @return array Returns the processed data with updated meals and calorie adjustments.
     */
    public function processFoodList(array $data)
    {
        $calorieBreakdown = $data['data']['calorieBreakdown']; // Total and per-meal calorie requirements.
        $result = $data['result']; // The list of meals grouped by day.
        $updatedResult = []; // Final list of meals with processed data.

        // Iterate through each day in the result data.
        foreach ($result as $day => $meals) {
            $currentCalorieOffset = 0; // Tracks the calorie offset to adjust meal calories for the day.

            // Iterate through each meal in the day.
            foreach ($meals as &$meal) {
                $mealId = $meal['meal_id']; // The ID of the current meal.
                $parentId = $meal['food_id']; // The parent food ID for the meal.


                $closestFood = null; // Initialize variable for finding the closest food.
                if ($parentId) {
                    // Find the required calories for this meal.
                    $requiredCalories = collect($calorieBreakdown['meals'])->firstWhere('meal_id', $mealId)['calories'] ?? 0;

                    // Adjust the required calories by the current offset.
                    $adjustedCalories = $requiredCalories + $currentCalorieOffset;

                    // Fetch related foods based on the parent ID.
                    $relatedFoods = Food::where('parent_id', $parentId)->get();

                    // Find the food with the closest calorie value to the adjusted requirement.
                    $closestFood = $relatedFoods->reduce(function ($closest, $food) use ($adjustedCalories) {
                        $currentDifference = abs($food->calories - $adjustedCalories);

                        if (!$closest || $currentDifference < abs($closest->calories - $adjustedCalories)) {
                            return $food; // Update the closest food if the current food is closer.
                        }

                        return $closest;
                    });
                }

                if ($closestFood) {
                    // Calculate the difference between the food's calories and the adjusted requirement.
                    $calorieDifference = $adjustedCalories - $closestFood->calories ;

                    // Add the selected food details to the meal.
                    $meal['final_food_id'] = $closestFood->id;
//                    $meal['final_food_name'] = $closestFood->name;
                    $meal['final_calories'] = $closestFood->calories;
                    $meal['calorie_difference'] = $calorieDifference;
                    $meal['adjusted_calories'] = $adjustedCalories;

                    // Update the calorie offset for the next meal in the day.
                    $currentCalorieOffset = $calorieDifference;
                } else {
                    // If no suitable food is found, set default values.
                    $meal['final_food_id'] = null;
                    $meal['final_food_name'] = 'Not Found';
                    $meal['final_calories'] = 0;
                    $meal['calorie_difference'] = 0;
                    $currentCalorieOffset = 0; // Reset the calorie offset for the next meal.
                }
            }
            // Add the updated meals for the day to the result array.
            $updatedResult[$day] = $meals;
        }

        // Return the processed data with updated results.
        return [
            'data' => $data['data'],
            'result' => $updatedResult
        ];
    }

    /**
     * Calculates the total daily calories for each day in the diet plan and appends a summary row to the result.
     *
     * @param array $data The input data containing meal details grouped by day.
     * @return array Returns the updated data with daily calorie summaries added to each day.
     */
    private function calculateDailyCalories(array $data)
    {
        $result = $data['result']; // Retrieve the list of meals grouped by day.
        $updatedResult = []; // Initialize an array to store the updated result with daily calorie summaries.

        // Iterate through each day in the result data.
        foreach ($result as $day => $meals) {
            $dailyCalories = 0; // Initialize the daily calorie counter.

            // Calculate the total calories for all meals in the current day.
            foreach ($meals as $meal) {
                $dailyCalories += $meal['final_calories'] ?? 0; // Add the calories of the current meal.
            }

            // Add a summary row to the meals for the current day.
            $meals[] = [
                'meal' => 'نتیجه', // Label for the summary row.
                'meal_id' => null, // No specific meal ID for the summary.
                'food_id' => null, // No specific food ID for the summary.
                'day' => $day, // The current day number.
                'final_food_id' => null, // No specific food selected for the summary.
                'final_food_name' => 'جمع کل کالری', // Label indicating the total calorie count.
                'final_calories' => $dailyCalories, // The total calories for the day.
                'calorie_difference' => null, // No calorie difference for the summary row.
            ];

            // Store the updated meals (with the summary) for the current day.
            $updatedResult[$day] = $meals;
        }

        // Return the updated data, including the original data and the updated results with summaries.
        return [
            'data' => $data['data'], // Original data (unchanged).
            'result' => $updatedResult, // Updated result with daily calorie summaries.
        ];
    }

    /**
     * @param $day
     * @param DietRequest $dietRequest
     * @param $foodsGroupedByMeal
     * @param $selectedFoodsPerMeal
     * @return array
     */
    private function generateDailyPlan($day, DietRequest $dietRequest, &$foodsGroupedByMeal, &$selectedFoodsPerMeal)
    {
        // Retrieve the meals in the diet plan, ordered by priority.
        $meals = $dietRequest->dietPlan->meals()->orderBy('priority')->get();
        $usedFoodsToday = []; // Keep track of foods already used for the current day.
        $dailyPlan = []; // Initialize the daily plan for this day.


        // Iterate through each meal in the diet plan.
        foreach ($meals as $meal) {
            $mealId = $meal->id; // Get the ID of the current meal.

            // Reset the list of selected foods if it is empty.
            if (empty($selectedFoodsPerMeal[$mealId])) {
                $selectedFoodsPerMeal[$mealId] = $foodsGroupedByMeal[$mealId];
            }

            // Select foods that haven't already been used today.
            $availableFoods = array_diff($selectedFoodsPerMeal[$mealId], $usedFoodsToday);

            // If no foods are available, reset the list of selected foods for this meal.
            if (empty($availableFoods)) {
                // ریست کردن لیست اگر تمام غذاها استفاده شده باشند
                $selectedFoodsPerMeal[$mealId] = $foodsGroupedByMeal[$mealId];
                $availableFoods = array_diff($selectedFoodsPerMeal[$mealId], $usedFoodsToday);
            }

            // Pick the first food from the available list.
            $selectedFood = array_shift($availableFoods);

            // Remove the selected food from the list of available foods for this meal.
            $selectedFoodsPerMeal[$mealId] = array_diff($selectedFoodsPerMeal[$mealId], [$selectedFood]);

            // Add the selected food to the list of foods used today.
            $usedFoodsToday[] = $selectedFood;

            // Save the meal information and selected food in the daily plan.
            $dailyPlan[] = [
                'meal' => $meal->name, // Name of the meal (e.g., breakfast, lunch, dinner).
                'meal_id' => $meal->id, // ID of the meal.
                'food_id' => $selectedFood, // ID of the selected food.
//                'availableFoods' => $availableFoods, // Remaining foods available for this meal.
                'day' => $day, // Current day number.
            ];
        }

        // Return the generated daily meal plan.
        return $dailyPlan;
    }

    public function countNullFoodId($inputArray): int
    {
        // Check if the 'data.result' key exists in the input array
        if (!isset($inputArray['data']['result'])) {
            return 0; // Return 0 if the key doesn't exist
        }

        $result = $inputArray['data']['result'];

        // Initialize a counter for items with food_id equal to null
        $count = 0;

        // Loop through each day in the result
        foreach ($result as $items) {
            // Loop through each item in the day's array
            foreach ($items as $item) {
                // Check if 'food_id' exists and its value is null
                if (array_key_exists('food_id', $item) && $item['food_id'] === null) {
                    $count++; // Increment the counter
                }
            }
        }

        return $count; // Return the total count
    }

    public function generateDiet($user, $dietPlanModel, $targetPlan = null): void
    {
        $dietPlan = app('dietService')->insertUserDietPlan($user, $dietPlanModel, targetPlan: $targetPlan);
        if ($dietPlan['status']) {
            $makeDiet = app('dietService')->makeDietFromFoods($dietPlan['dietRequestModel']);
        }
        $requestDiet = $dietPlan['dietRequestModel'];
        if (app('dietService')->countNullFoodId($makeDiet) == 0) {
            $dietCount = $dietPlanModel->day_count;

            $requestDiet->update([
                'active' => true,
                'status' => DietRequestStatusEnum::ACTIVE,
                'start_date' => Carbon::now()->toDateTimeString(),
                'end_date' => Carbon::now()->addDays($dietCount)->toDateTimeString(),
            ]);
            $hour = Carbon::now()->hour;
            if ($hour > 12) {
                $dietCount++;
                $user->popup = PopupEnum::STARTING_THE_DIET_TOMORROW->value;
            }
            $sessionNumber = $this->getNumberDiets($user);
            $user->dispatchDietReminders($sessionNumber,$dietCount);


        } else {
            $user->popup = PopupEnum::REJECTED_DIET->value;
            $requestDiet->update([
                'active' => false,
                'status' => DietRequestStatusEnum::REJECT_BY_SYSTEM_HAVE_ERROR,
            ]);
        }
    }

    public function getNumberDiets($userId)
    {
        $dietRequests = DietRequest::where('user_id', $userId)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();

        $validDietRequests = [];
        foreach ($dietRequests as $dietRequest) {
            $isValid = true;
            foreach ($validDietRequests as $validRequest) {
                if (
                    ($dietRequest->start_date <= $validRequest->end_date) &&
                    ($dietRequest->end_date >= $validRequest->start_date)
                ) {
                    $isValid = false;
                    break;
                }
            }

            if ($isValid) {
                $validDietRequests[] = $dietRequest;
            }
        }

        return count($validDietRequests);
    }

    public function gtePersianConditionName($conditions): array
    {
        $res = [];
        if (count($conditions)) {
            foreach ($conditions as $key => $condition) {
                $conditionKey = ConditionKeyEnum::tryFrom($key);
                if ($conditionKey->name == "DISEASE") {
                    $values = Disease::whereIn('id', $condition)->pluck('name')->toArray();
                    $conditionsList = $values;
                } else {

                    $conditionsList = Condition::where('key', $key)->first()?->options['items']['values'];
                    if (count($condition) && $conditionsList) {
                        $result = array_intersect_key($conditionsList, array_flip($condition));
                    }

                    $values = $result;
                }

                $res[] = [
                    'id' => $key,
                    'name' => $conditionKey->getName(),
                    'key' => $conditionKey->name,
                    'user_conditions' => $condition,
                    'list_conditions' => $conditionsList,
                    'value' => $values
                ];
            }
        }
        return $res;
    }
}
