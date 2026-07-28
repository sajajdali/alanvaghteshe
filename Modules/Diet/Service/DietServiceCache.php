<?php

namespace Modules\Diet\Service;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Enum\DietRequestStatusEnum;

trait DietServiceCache
{
    public function userWaterConsumptionPerDay($user, $dateSelect = null , $forceForget = true){
        if ($dateSelect == null){
            $date = Carbon::today()->toDateString(); // Get today's date
        } else {
            $date = Carbon::parse($dateSelect)->toDateString();
        }

        // Cache key for today's consumption data
        $cacheKey = 'user_water_consumption_' . $user->id . '_' . $date;

        // Check if the data is already cached and forceForget is false
        if (!$forceForget && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // If data is not cached or forceForget is true, retrieve it from the database
        $consumption_today = $user->waterConsumptions()
            ->whereDate('consumed_at', $date)
            ->select('quantity')
            ->first();

        $consumptionData = $consumption_today ? $consumption_today->quantity : 0;

        // If forceForget is true, forget the cache
        if ($forceForget) {
            Cache::forget($cacheKey);
        } else {
            // Cache the data with expiration time set to the end of the day
            $expiration = Carbon::tomorrow()->endOfDay();
            Cache::put($cacheKey, $consumptionData, $expiration);
        }

        return [
            'water_consumption' => (int) $consumptionData ,
            'amount_daily' => $this->calculateWaterIntake($user)
        ];
    }

    public function calculateWaterIntake($user): int
    {
        // Cache key and tag for the user

        // Retrieve the user's current weight
        $weight = $this->getCurrentWeight($user);
        if ($weight == null || $weight == 0){
            return 0;
        }

        // Calculate the minimum water intake in milliliters
        $minWaterMl = $weight * 30;

        // Convert milliliters to cups (1 cup = 250 ml)
        $minCups = $minWaterMl / 250;

        // Round and cast to integer
        $waterIntake = (int) round($minCups);

        // Cache the data with expiration time set to the end of the day

        return $waterIntake;
    }

    public function userBmi($user , $forget = false)
    {
        if ($forget){
            Cache::tags(['user:'.$user->id])->forget('bmi');
        }
        return Cache::tags(['user:'.$user->id])->remember('bmi', now()->addDay(), function () use ($user) {
            return $this->computeCalorie($user , computeWithLastDiet : true);
        });
    }

    public function weeklyFeedHistory($user)
    {
        return $this->userFoodConsumptionPerWeek($user);
    }

    public function userFoodConsumptionPerWeek($user, $dateSelect = null, $forceForget = false)
    {
        if ($dateSelect == null) {
            $endDate = Carbon::today();
        } else {
            $endDate = Carbon::parse($dateSelect);
        }

        $startDate = $endDate->copy()->subDays(6); // Calculate the start date (7 days before the end date)

        // Cache key for the user's weekly consumption data
        $cacheKey = 'user_food_consumption_week_' . $user->id . '_' . $endDate->toDateString();

        // Cache tag for the user
        $cacheTag = 'user_' . $user->id;

        // Check if the data is already cached and forceForget is false
        if ( env('DISABLE_MAKE_JSON_FILE') !== true && Cache::tags($cacheTag)->has($cacheKey)) {
            return Cache::tags($cacheTag)->get($cacheKey);
        }

        // Retrieve the consumption data for each day in the week
        $consumptionData = [];
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $consumption = $user->foodConsumptions()
                ->whereDate('consumed_at', $date)
                ->selectRaw('SUM(calories) as total_calories, SUM(protein) as total_protein, SUM(fiber) as total_fiber, SUM(carb) as total_carbohydrates, SUM(fat) as total_fat')
                ->first();

            $totalProtein = round($consumption->total_protein ?? 0);
            $totalFiber = round($consumption->total_fiber ?? 0);
            $totalCarb = round($consumption->total_carbohydrates ?? 0);
            $totalFat = round($consumption->total_fat ?? 0);

            $total = $totalProtein + $totalFiber + $totalCarb + $totalFat;

            $percentProtein = $total > 0 ? round(($totalProtein / $total) * 100) : 0;
            $percentFiber = $total > 0 ? round(($totalFiber / $total) * 100) : 0;
            $percentCarb = $total > 0 ? round(($totalCarb / $total) * 100) : 0;
            $percentFat = $total > 0 ? round(($totalFat / $total) * 100) : 0;

            // Adjust percentages to ensure the sum is 100%
            $sumPercentages = $percentProtein + $percentFiber + $percentCarb + $percentFat;
            if ($sumPercentages > 100) {
                $diff = $sumPercentages - 100;
                // Reduce the largest percentage by the difference
                if ($percentProtein >= $percentFiber && $percentProtein >= $percentCarb && $percentProtein >= $percentFat) {
                    $percentProtein -= $diff;
                } elseif ($percentFiber >= $percentProtein && $percentFiber >= $percentCarb && $percentFiber >= $percentFat) {
                    $percentFiber -= $diff;
                } elseif ($percentCarb >= $percentProtein && $percentCarb >= $percentFiber && $percentCarb >= $percentFat) {
                    $percentCarb -= $diff;
                } else {
                    $percentFat -= $diff;
                }
            }

            // Calculate start and end values for each percentage
            $start = 0;
            $percentData = [];

            $percentData['protein'] = [
                'amount' => $percentProtein,
                'start' => $start,
                'end' => $start + $percentProtein
            ];
            $start = $percentData['protein']['end'];

            $percentData['fiber'] = [
                'amount' => $percentFiber,
                'start' => $start,
                'end' => $start + $percentFiber
            ];
            $start = $percentData['fiber']['end'];

            $percentData['carb'] = [
                'amount' => $percentCarb,
                'start' => $start,
                'end' => $start + $percentCarb
            ];
            $start = $percentData['carb']['end'];

            $percentData['fat'] = [
                'amount' => $percentFat,
                'start' => $start,
                'end' => $start + $percentFat
            ];

            $consumptionData[$date->toDateString()] = [
                'total_calories' => max(0, round($consumption->total_calories ?? 0)),
                'total_protein' => max(0, $totalProtein),
                'total_fiber' => max(0, $totalFiber),
                'total_carb' => max(0, $totalCarb),
                'total_fat' => max(0, $totalFat),
                'percent' => max(0, $percentData),
            ];
        }

        // If forceForget is true, forget the cache
        if ($forceForget) {
            Cache::tags($cacheTag)->forget($cacheKey);
        } else {
            // Cache the data with expiration time set to the end of the day
            $expiration = Carbon::tomorrow()->endOfDay();
            Cache::tags($cacheTag)->put($cacheKey, $consumptionData, $expiration);
        }

        return $consumptionData;
    }

    public function userFoodConsumptionPerWeekOld($user, $dateSelect = null, $forceForget = true)
    {
        if ($dateSelect == null) {
            $endDate = Carbon::today();
        } else {
            $endDate = Carbon::parse($dateSelect);
        }

        $startDate = $endDate->copy()->subDays(6); // Calculate the start date (7 days before the end date)

        // Cache key for the user's weekly consumption data
        $cacheKey = 'user_food_consumption_week_' . $user->id . '_' . $endDate->toDateString();

        // Cache tag for the user
        $cacheTag = 'user_' . $user->id;

        // Check if the data is already cached and forceForget is false
        if (!$forceForget && Cache::tags($cacheTag)->has($cacheKey)) {
            return Cache::tags($cacheTag)->get($cacheKey);
        }

        // Retrieve the consumption data for each day in the week
        $consumptionData = [];
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $consumption = $user->foodConsumptions()
                ->whereDate('consumed_at', $date)
                ->selectRaw('SUM(calories) as total_calories, SUM(protein) as total_protein, SUM(fiber) as total_fiber, SUM(carb) as total_carbohydrates, SUM(fat) as total_fat')
                ->first();

            $consumptionData[$date->toDateString()] = [
                'total_calories' => round($consumption->total_calories ?? 0),
                'total_protein' => round($consumption->total_protein ?? 0),
                'total_fiber' => round($consumption->total_fiber ?? 0),
                'total_carb' => round($consumption->total_carbohydrates ?? 0),
                'total_fat' => round($consumption->total_fat ?? 0),
                'percent' => [
                    'protein' => 80,
                    'fiber' => 20,
                    'carb' => 15,
                    'fat' => 5,
                ]
            ];
        }

        // If forceForget is true, forget the cache
        if ($forceForget) {
            Cache::tags($cacheTag)->forget($cacheKey);
        } else {
            // Cache the data with expiration time set to the end of the day
            $expiration = Carbon::tomorrow()->endOfDay();
            Cache::tags($cacheTag)->put($cacheKey, $consumptionData, $expiration);
        }

        return $consumptionData;
    }

    public function getUserMeals($user, $calorie=1, $dateSelect = null, $forceForget = false)
    {
        if ($dateSelect == null) {
            $date = Carbon::today()->toDateString(); // Get today's date
        } else {
            $date = Carbon::parse($dateSelect)->toDateString();
        }

        // Cache key based on user ID and calorie parameter
        $cacheKey = 'user_meals_' . $user->id;
        $cacheTag = 'user_' . $user->id;

        // Check if the data is already cached and forceForget is false

        if (disableCache()){
            $forceForget = true;
        }
        if (!$forceForget && Cache::tags($cacheTag)->has($cacheKey)) {
            //return Cache::tags($cacheTag)->get($cacheKey);
        }

        // Retrieve the latest diet plan with general pattern 1
        $activeDiet = app('dietService')->userActiveDiet($user);
        if ($activeDiet) {
            $dietPlan = $activeDiet->dietPlan;
        } else {
            $dietPlan = DietPlan::where('general_pattern', 1)->orderByDesc('id')->first();
        }

        // active package

        if (!$dietPlan) {
            return [];
        }

        $activeDiet = $this->userActiveDiet($user);

        $mealResult = [];
        // Get meals associated with the diet plan, ordered by priority
        foreach ($dietPlan->meals()->orderBy('priority')->get() as $meal) {
            $consumable = $user->foodConsumptions()->where('meal_id', $meal->id)->whereDate('consumed_at',$date)->sum('calories');
            $getMealName = $meal->name;
            if ($activeDiet){
                $getMealName = app(DietService::class)->handleMealName($activeDiet , $meal);
            }

            $showThisMeal = true;
            if (isset($dietPlan->detail[DietPlan::IS_ATHLETE_MEAL]) && count($dietPlan->detail[DietPlan::IS_ATHLETE_MEAL])){
                $athleteMeal = $dietPlan->detail[DietPlan::IS_ATHLETE_MEAL];
                if (isset($athleteMeal)){
                    if (array_key_exists($meal->id,$athleteMeal)){
                        $showThisMeal = false;
                        $listTrainingDays = isset($activeDiet->detail[DietRequest::KEY_DETAIL_TRAINING_DAYS]) ? $activeDiet->detail[DietRequest::KEY_DETAIL_TRAINING_DAYS] : [];
                        $theDay = $dateSelect ?? Carbon::now()->format('Y-m-d');
                        if (in_array($theDay, $listTrainingDays)){
                            $showThisMeal = true;
                        }
                    }
                }
            }

            if ($showThisMeal) {
                $mealResult[] = [
                    'id' => $meal->id,
                    'name' => $getMealName,
                    'image' => $meal->icon_url,
                    'icon' => $meal->icon_url,
                    'calories_consumed' => $consumable,
                    'percent' => $meal->pivot->calorie_percent,
                    'active' => (bool)$user->activePackage(),
                    'suggested' => (string)'کالری پیشنهادی ' . round(($meal->pivot->calorie_percent * $calorie) / 100),
                ];
            }
        }

        // Cache the data with expiration time set to the end of the day
        $expiration = Carbon::today()->endOfDay();
        Cache::tags($cacheTag)->put($cacheKey, $mealResult, $expiration);

        return $mealResult;
    }

    public function userFoodConsumptionPerMeal($user, $mealId, $dateSelect = null, $forceForget = true)
    {
        if ($dateSelect == null) {
            $selectedDate = Carbon::today();
        } else {
            $selectedDate = Carbon::parse($dateSelect);
        }

        // Cache key for the user's daily meal consumption data
        $cacheKey = 'user_food_consumption_' . $user->id . '_meal_' . $mealId . '_' . $selectedDate->toDateString();

        // Cache tag for the user
        $cacheTag = 'user_' . $user->id;

        // Check if the data is already cached and forceForget is false
        if (!$forceForget && Cache::tags($cacheTag)->has($cacheKey)) {
            return collect(Cache::tags($cacheTag)->get($cacheKey));
        }

        // Retrieve the consumption data for the specific meal on the selected date
        $consumptionData = $user->foodConsumptions()
            ->where('meal_id', $mealId)
            ->whereDate('consumed_at', $selectedDate)
            ->get();

        // If forceForget is true, forget the cache
        if ($forceForget) {
            Cache::tags($cacheTag)->forget($cacheKey);
        } else {
            // Cache the data with expiration time set to the end of the day
            $expiration = Carbon::tomorrow()->endOfDay();
            Cache::tags($cacheTag)->put($cacheKey, $consumptionData, $expiration);
        }

        return $consumptionData;
    }



}
