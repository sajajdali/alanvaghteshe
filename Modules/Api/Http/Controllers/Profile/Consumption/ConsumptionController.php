<?php

namespace Modules\Api\Http\Controllers\Profile\Consumption;

use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Modules\Api\app\Http\Requests\Api\Requests\Consumption\StoreConsumptionRequest;
use Modules\Api\app\Http\Requests\Api\Requests\Consumption\StoreWaterConsumptionRequest;
use Modules\Api\app\Resources\Api\Consumption\BasicFoodResource;
use Modules\Api\app\Resources\Api\Consumption\FoodCategoryResource;
use Modules\Api\app\Resources\Api\Consumption\FoodsPaginateResource;
use Modules\Api\app\Resources\Api\Consumption\MealResource;
use Modules\Api\app\Resources\Api\Consumption\UserFoodsResource;
use Modules\Api\app\Resources\RequestDietDetailResourcev2;
use Modules\Api\Enum\CacheEnum;
use Modules\Api\Trait\ApiDietTrait;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Diet\app\Jobs\CreateJsonDietJob;
use Modules\Diet\app\Models\FoodConsumption;
use Modules\Diet\app\Models\NutritionTip;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodCategory;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\Meal;
use Verta;

class ConsumptionController extends Controller
{
    use ApiHandlerTrait , ApiDietTrait;

    public function show(BasicFood $basicFood)
    {
        return $this->ok(BasicFoodResource::make($basicFood));
    }

    public function cheatMeal(Meal $meal)
    {
        $today = Carbon::today();
        $user = auth()->user();
        $activeDiet = $user->activeDiet();
        if ($activeDiet){
            if (!app('dietService')->canCheat($activeDiet)){
                return $this->badRequest([
                    'message' => 'شما تمامی چیت های خود را انجام داده اید'
                ]);
            }
            $dietRequestDetail = $activeDiet->dietRequestDetails()
                ->where('meal_id', $meal->id)
                ->whereDate('date_of_day', $today->toDateString());

            $dietRequestDetail->update([
                'cheat_meal' => 1
            ]);
            $res = app('dietService')->getOneMeal($user , $meal , Carbon::today());
            dispatch_sync(new CreateJsonDietJob($dietRequestDetail->first()->dietRequest));

            return $this->ok([
                'status' => true,
                'message' => 'وعده چیت شما ذخیره شد',
                'food' => $res
            ]);


        } else {
            return $this->badRequest([
                'message' => 'رژیم فعال یافت نشد'
            ]);
        }
    }

    public function meals()
    {
        $user = auth()->user();
        $userMeals = app('dietService')->getUserMeals($user);
        return $this->ok([
            'data' => $userMeals
        ]);

        // old version
//        return $this->ok([
//            'data' => MealResource::collection(Meal::orderBy('priority')->get())
//        ]);
    }
    public function createFood(StoreConsumptionRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $time = explode(":", Carbon::now()->toTimeString());
        $date = $request->has('date')
            ? Carbon::parse($request->input('date'))->setTime($time[0], $time[1], $time[2])->toDateTimeString()
            : Carbon::now();

        $consumable = $request->input('type') == '2'
            ? Food::findOrFail($request->input('food_id'))
            : BasicFood::findOrFail($request->input('food_id'));

        $foodUnitId = $request->input('unit_id');
        if ($foodUnitId == 3){
            $foodUnit = FoodUnit::find(3);
            $quantityPerUnit = 1;
        } else {
            $foodUnit = $consumable->units()->where('food_units.id', $foodUnitId)->firstOrFail();
            $quantityPerUnit = $foodUnit->pivot->quantity_per_unit;

        }

        $quantity = $request->input('quantity');
        $mealId = $request->input('meal_id');
        $response = $this->insertConsumedFood($user ,$consumable, $foodUnitId , $quantityPerUnit, $mealId , $quantity , $date);


        return $this->ok([
            'message' => 'غذا با موفقیت اضافه شد',
            'response_id' => $response->id,
        ]);
    }

    public function insertConsumedFood($user , $food ,$foodUnit ,$quantityPerUnit , $mealId  , $quantity , $date)
    {
        $consumable = $food;
        $calories = max(0, clcPerUnit($consumable->food_fact['CALORIE'], $quantityPerUnit) * $quantity);
        $protein = max(0, clcPerUnit($consumable->food_fact['PROTEIN'], $quantityPerUnit) * $quantity);
        $fat = max(0, clcPerUnit($consumable->food_fact['FAT'], $quantityPerUnit) * $quantity);
        $carb = max(0, clcPerUnit($consumable->food_fact['CARBOHYDRATE'], $quantityPerUnit) * $quantity);
        $fiber = max(0, clcPerUnit($consumable->food_fact['FIBER'], $quantityPerUnit) * $quantity);

        return $user->foodConsumptions()->create([
            'consumable_id' => $consumable->id,
            'consumable_type' => get_class($consumable),
            'meal_id' => $mealId,
            'food_unit_id' => $foodUnit,
            'quantity' => $quantity,
            'consumed_at' => $date,
            'calories' => $calories,
            'protein' => $protein,
            'fat' => $fat,
            'carb' => $carb,
            'fiber' => $fiber,
        ]);
    }

    public function deleteFood(FoodConsumption $foodConsumption): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        if ($foodConsumption->user->id != $user->id){
            return $this->badRequest([
                'message' => 'این وعده متعلق به این کاربر نیست و شما امکان حذف آن را ندارید'
            ]);
        }

        $foodConsumption->delete();
        return $this->ok([
            'message' => 'غذا با موفقیت حذف شد شد',
        ]);

    }

    public function createWater(StoreWaterConsumptionRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        $time = explode(":", Carbon::now()->toTimeString());
        $date = $request->has('date') ? Carbon::parse($request->input('date'))->setTime($time[0] , $time[1] , $time[2]) : Carbon::now();


        if($user->waterConsumptions()->whereDate('consumed_at' ,$date)->count()){
            $user->waterConsumptions()->whereDate('consumed_at', $date)->update([
                'quantity' => $request->input('quantity'),
            ]);
        } else {
            $user->waterConsumptions()->create([
                'quantity' => $request->input('quantity'),
                'consumed_at' => $date
            ]);
        }


        return $this->ok([
            'message' => 'آب با موفقیت اضافه شد',
        ]);
    }

    public function category(): \Illuminate\Http\JsonResponse
    {
        $foodCategories = Cache::rememberForever(CacheEnum::FOOD_CATEGORY->value, function () {
            return FoodCategoryResource::collection(FoodCategory::orderByDesc('id')->get());
        });
        return $this->ok([
            'data' => $foodCategories
        ]);
    }


    public function favorites(): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();

        $cacheKey = CacheEnum::FAVORITE_FOODS->parameter((string)$userId);

        $favoriteFoods = Cache::rememberForever($cacheKey, function () {
            $raw = auth()->user()->favorite_basic_foods;
            return $raw ? json_decode($raw, true) : [];
        });

        if (empty($favoriteFoods)) {
            return $this->ok([
                'status' => false,
                'message' => 'هیچ غذایی در لیست علاقه مندی های شما وجود ندارد',
                'list' => null,
            ]);
        }

        $basicFoodsKey = 'basic_foods_data_user_' . $userId; // یا هش
        $basicFoodsData = Cache::rememberForever($basicFoodsKey, function () use ($favoriteFoods) {
            return BasicFood::whereIn('id', $favoriteFoods)->get();
        });

        return $this->generatePaginatedResponse($basicFoodsData, $favoriteFoods);
    }
    private function generatePaginatedResponse($data, $favoriteFoods = null)
    {
        if (!($data instanceof \Illuminate\Database\Eloquent\Collection)) {
            $data = collect($data);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = self::PAGINATE;
        $currentPageItems = $data->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedData = new LengthAwarePaginator($currentPageItems, $data->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        $resource = FoodsPaginateResource::make($paginatedData);

        // بررسی اگر $favoriteFoods مقداردهی شده باشد
        if ($favoriteFoods !== null) {
            $resource->additional(['favorite_foods' => $favoriteFoods]);
        }

        return $this->ok($resource);
    }

    public function foods(FoodCategory $category, Request $request)
    {
        $searchQuery = $request->input('q');
        $cacheKey = CacheEnum::BASIC_FOODS->parameter($category->id);

        $basicFoodsData = Cache::tags('basic_foods')->rememberForever($cacheKey, function () use ($category) {
            return $category->basicFoods()->select(['basic_foods.id', 'basic_foods.name'])->get();
        });

        if (!empty($searchQuery)) {
            $basicFoodsData = $basicFoodsData->filter(function ($food) use ($searchQuery) {
                return stripos($food->name, $searchQuery) !== false;
            });
        }

        $userId = auth()->id();
        $cacheKey = CacheEnum::FAVORITE_FOODS->parameter($userId);

        $favoriteFoods = Cache::rememberForever($cacheKey, function () use ($userId) {
            $favoriteFoods = auth()->user()->favoriteBasicFoods;
            return $favoriteFoods ? json_decode($favoriteFoods) : [];
        });

        return $this->generatePaginatedResponse($basicFoodsData, $favoriteFoods);
    }
    public function searchFoods( Request $request)
    {
        $searchQuery = $request->input('q');

        $basicFoodsData =BasicFood::select(['basic_foods.id', 'basic_foods.name'])->get();

        if (!empty($searchQuery)) {
            $basicFoodsData = $basicFoodsData->filter(function ($food) use ($searchQuery) {
                return stripos($food->name, $searchQuery) !== false;
            });
        }


        return $this->generatePaginatedResponse($basicFoodsData);
    }

    public function foodOfMeal(Meal $meal , Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $currentDay = $date->toDateString();
        $nextDay = $date->copy()->addDay()->toDateString();
        $prevDay = $date->copy()->subDay()->toDateString();

        $userFood = app('dietService')->userFoodConsumptionPerWeek($user , $date);
        $userBmi = app('dietService')->userBmi($user);
        $userFoodThisDay = $userFood[$currentDay];

        //the foods of this meal

        $userFoods = null;
        $userFoodsPerMeal = app('dietService')->userFoodConsumptionPerMeal($user, $meal->id, $date);
        if ($userFoodsPerMeal){
            $userFoods = UserFoodsResource::collection($userFoodsPerMeal);
        }

        return $this->ok([
            'dates' => [
                'title' => (Carbon::today()->toDateString() == $currentDay ? 'امروز, ' : '') .verta($date)->format('d F'),
                'today' => Carbon::now()->toDateString(),
                'current_day' => $currentDay,
                'next_day' => $nextDay,
                'prev_day' => $prevDay,
            ],

            'top_chart' => [
                'calorie_meal_day' => [
                    'max_allowed' => $userBmi->calorie,
                    'consumed' => $userFoodThisDay['total_calories'],
                    'percent' => $this->calculatePercentage($userBmi->calorie, $userFoodThisDay['total_calories'])
                ],
                'protein' => [
                    'max_allowed' => $userBmi->unitsNeeded->protein,
                    'consumed' => $userFoodThisDay['total_protein'],
                    'percent' => $this->calculatePercentage($userBmi->unitsNeeded->protein, $userFoodThisDay['total_protein'])
                ],
                'carb' => [
                    'max_allowed' => $userBmi->unitsNeeded->carb,
                    'consumed' => $userFoodThisDay['total_carb'],
                    'percent' => $this->calculatePercentage($userBmi->unitsNeeded->carb, $userFoodThisDay['total_carb'])
                ],
                'fiber' => [
                    'max_allowed' => $userBmi->unitsNeeded->fiber,
                    'consumed' => $userFoodThisDay['total_fiber'],
                    'percent' => $this->calculatePercentage($userBmi->unitsNeeded->fiber, $userFoodThisDay['total_fiber'])
                ],
                'fat' => [
                    'max_allowed' => $userBmi->unitsNeeded->fat,
                    'consumed' => $userFoodThisDay['total_fat'],
                    'percent' => $this->calculatePercentage($userBmi->unitsNeeded->fat, $userFoodThisDay['total_fat'])
                ]
            ],
            'nutrition_tip' => $this->getNutritionTip(),
            'foods' => $userFoods,
//            'prescribed_food' => $this->getOneMeal($user , $meal , $date),
            'prescribed_food' => app('dietService')->getOneMeal($user , $meal , $date),
        ]);
    }

    private function getNutritionTip()
    {
        $nutritionTip = NutritionTip::inRandomOrder()->first();
        if ($nutritionTip){
            return  $nutritionTip->description;
        }
        return null;

    }



    public function favorite(BasicFood $basicFood)
    {
        $user = auth()->user();
        $favoriteFoods = $user->favoriteBasicFoods;
        if ($favoriteFoods){
            $favoriteRecipe = json_decode($favoriteFoods , true);
            if (in_array( $basicFood->id , $favoriteRecipe)){
                $favoriteRecipe = array_filter($favoriteRecipe, fn($item) => $item !== $basicFood->id);
            } else {
                $favoriteRecipe[] = $basicFood->id;
            }
        } else {
            $favoriteRecipe = [$basicFood->id];
        }
        $favoriteRecipe = array_values( array_unique($favoriteRecipe));
        $user->favoriteBasicFoods = json_encode($favoriteRecipe);

        return $this->ok([
            'status' => true
        ]);

    }


}
