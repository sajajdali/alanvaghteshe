<?php

namespace Modules\Diet\Service;

use Carbon\Carbon;
use File;
use Illuminate\Database\Eloquent\Collection;
use Modules\Api\app\Resources\diet_request_detail\RequestDietDetailResource;
use Modules\Api\app\Resources\RequestDietDetailResourcev2;
use Modules\Diet\app\Jobs\CreateJsonDietJob;
use Modules\Diet\app\Jobs\MakeRejimJob;
use Modules\Diet\app\Models\FoodConsumption;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Diet\Enum\FoodTypeEnum;
use Modules\Diet\Enum\MainNutritionEnum;
use Modules\Diet\Traits\MakeDietV2;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;
use Str;

class DietService
{

    use DietServiceCache , MakeDietV2;

    private $app;

    /**
     * @param $app
     */
    public function __construct($app = null)
    {
        if ($app == null) {
            $app = app();
        }
        $this->app = $app;
    }

    public function fastingDietStart(DietRequest $dietRequest): ?array
    {
        if (
            isset($dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING])
            &&
            isset($dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['status']) &&
            $dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['status']
        ) {
            $startAt = $dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['start_at'];
            if (isset($dietRequest->detail['fasting']) && isset($dietRequest->detail['fasting']['start_at'])) {
                $startAt = $dietRequest->detail['fasting']['start_at'];
            }
            return [
                'start_at' => (string) $startAt,
                'fasting_hours' => (string) ( $dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['fasting_hours'] ?? 16),
            ];
        }
        return null;
    }

    public function handleFastingTimer($user)
    {
        $fastingResource = null;
        $userActiveDiet = $this->userActiveDiet($user);

        if ($userActiveDiet) {
            $fastingDietStart = app(DietService::class)->fastingDietStart($userActiveDiet);

            if ($fastingDietStart) {
                $startHour     = (int) $fastingDietStart['start_at'];      // مثلا 22
                $fastingHours  = (int) $fastingDietStart['fasting_hours']; // مثلا 16

                $now = Carbon::now();

                // شروع امروز در ساعت startHour
                $todayStart = $now->copy()->setTime($startHour, 0, 0);

                // اگر هنوز به start امروز نرسیدیم، «آخرین شروع» دیروزِ همون ساعته
                $lastStart = $now->gte($todayStart) ? $todayStart : $todayStart->copy()->subDay();

                // پایان بازهٔ فست برای همین چرخه
                $endFasting = $lastStart->copy()->addHours($fastingHours);

                // شروع بعدی (چرخهٔ بعدی)
                $nextStart = $lastStart->copy()->addDay();

                // داخل فست بودن: [lastStart, endFasting)  (شروع شامل، پایان غیرشامل)
                $inFasting = $now->gte($lastStart) && $now->lt($endFasting);

                if ($inFasting) {
                    // الان فست هست؛ تا پایان فست بشمار
                    $boundary      = $endFasting;
                    $isTimeFasting = false;
                    $title         = 'ساعت فستینگ';
                    $description   = 'الان نباید چیزی بخورید.';
                } else {
                    // الان بازهٔ خوردن هست؛ تا شروع بعدی (۲۲:۰۰) بشمار
                    $boundary      = $nextStart;
                    $isTimeFasting = true;
                    $title         = 'ساعت مجاز برای خوردن';
                    $description   = 'الان می‌توانید بخورید.';
                }

                // شمارش معکوس به ثانیه (Ceil تا زودتر از موعد صفر نشه)
                $countdownTimer = max(0, $now->diffInSeconds($boundary, false));

                $fastingResource = [
                    'now'            => $now->toDateTimeString(),
                    // start/end مربوط به «چرخهٔ فعلی» هستند، نه لزوماً امروز
                    'start'          => $lastStart->toDateTimeString(),
                    'end'            => $endFasting->toDateTimeString(),
                    'next_start'     => $nextStart->toDateTimeString(),
                    'title'          => $title,
                    'description'    => $description,
                    'countdown'      => (int) ceil($countdownTimer),
                    'is_time_fasting'=> $isTimeFasting,
                ];
            }
        }

        return $fastingResource;
    }
    public function replaceFood(DietRequestDetail $dietRequestDetail, BasicFood|Food $basicFood)
    {
        $dietRequest = $dietRequestDetail->dietRequest;
        $meal = $dietRequestDetail->meal;
        $mainNutrition = $dietRequestDetail->main_nutrition;
        if ($mainNutrition == MainNutritionEnum::special) {
            $modelCreate = [
                'diet_request_id' => $dietRequest->id,
                'meal_id' => $meal->id,
                'calories' => 0,
                'day_number' => $dietRequestDetail->day_number,
                'calculated_value' => 1,
                'number_of_unit' => 1,
                'main_nutrition' => MainNutritionEnum::special,
                'date_of_day' => $dietRequestDetail->date_of_day,
            ];
        } else {
            if ($basicFood instanceof Food) {
                $totalAmountPerUnit = $dietRequest->detail['caloriesAndUnits']['calorie'];
                $amountPerUnit = $dietRequest->dietPlan->meals()->where('meal_id', $meal->id)->first()->pivot->calorie_percent ?? 100;
                $amountNeed = round($totalAmountPerUnit * ($amountPerUnit / 100), 1);
                $foodUnitMultiple = self::findNearestSmallerMultiple($amountNeed, $basicFood->calories, 25, $basicFood->max_per_unit ?? null);
                $amountDifference = $foodUnitMultiple['difference'] ?? 0;

                if ($amountDifference > 0) {
                    $amountNeed += $amountDifference;
                }

                $numberOfUnit = $foodUnitMultiple['dividedInHalf'] ? $foodUnitMultiple['multiplications'] + 0.5 : $foodUnitMultiple['multiplications'];
                $mainNutritionDB = MainNutritionEnum::all;
                $calorie = (float)self::calculateFromMainUnitMultiple($basicFood->calories, $foodUnitMultiple);

            } else {

                $totalAmountPerUnit = $dietRequest->detail['caloriesAndUnits']['unitsNeeded'][$mainNutrition->name];
                $amountPerUnit = $dietRequest->dietPlan->meals()->where('meal_id', $meal->id)->first()->pivot->{$mainNutrition->name} ?? 100;
                $amountNeed = round($totalAmountPerUnit * ($amountPerUnit / 100), 1);
                $foodUnitMultiple = self::findNearestSmallerMultiple($amountNeed, self::calculationBasedOnTypeOfDivision($basicFood, $basicFood->{$mainNutrition->name}), 25, $basicFood->max_allowed ?? null);
                $numberOfUnit = $foodUnitMultiple['dividedInHalf'] ? $foodUnitMultiple['multiplications'] + 0.5 : $foodUnitMultiple['multiplications'];
                $mainNutritionDB = $mainNutrition->name;
                $quantityPerUnitTotal = ((float)self::calculateFromMainUnitMultiple($basicFood->quantity_per_unit, $foodUnitMultiple));
                $calorie = (($basicFood->calories / 100) * $quantityPerUnitTotal);
            }


            $modelCreate = [
                'diet_request_id' => $dietRequest->id,
                'meal_id' => $meal->id,
                'calories' => $calorie,
                'day_number' => $dietRequestDetail->day_number,
                'calculated_value' => $amountNeed,
                'number_of_unit' => $numberOfUnit,
                'main_nutrition' => $mainNutritionDB,
                'date_of_day' => $dietRequestDetail->date_of_day,
            ];

            if ($basicFood instanceof Food) {
                foreach (MainNutritionEnum::cases() as $nutritionList) {
                    $modelCreate[$nutritionList->name] = (float)self::calculateFromMainUnitMultiple($basicFood->{$nutritionList->name}, $foodUnitMultiple);
                }
            } else {
                foreach (MainNutritionEnum::cases() as $nutritionList) {
                    if ($nutritionList == $mainNutrition) {
                        $modelCreate[$mainNutrition->name] = round($foodUnitMultiple['result'], 1);
                    }
                    $modelCreate[$nutritionList->name] = (($basicFood->{$nutritionList->name} / 100) * $quantityPerUnitTotal);
                }
            }
        }

        $replaceFood = $basicFood->dietRequestDetails()->create($modelCreate);
        $dietRequestDetail->update([
            'replaced_parent_id' => $replaceFood->id
        ]);
        CreateJsonDietJob::dispatch($dietRequestDetail->dietRequest)->onQueue('low'); // job regenerate json file

        return [
            'status' => true,
            'diet_request_detail_inserted_id' => $replaceFood->id,
            'replace' => $basicFood
        ];


    }

    private function findNearestSmallerMultiple($mainNumber, $inputNumber, $maxIterations = 25)
    {
        $multiplier = 2;
        $result = $inputNumber * $multiplier;
        $multiplications = 1;

        while ($inputNumber != 0 && $result <= $mainNumber && $multiplications <= $maxIterations) {
            $multiplier++;
            $result = $inputNumber * $multiplier;
            $multiplications++;
        }
        $multiplications = $inputNumber === 0 ? 0 : $multiplications;

        $difference = abs($result - $mainNumber);

        // Adjust the result to be the nearest smaller multiple
        if ($result > $mainNumber) {
            $result -= $inputNumber;
            $difference = abs($result - $mainNumber);
        }

        // If possible, multiply by half
        $dividedInHalf = false;
        if (($inputNumber * .5) + $result <= $mainNumber) {
            $dividedInHalf = true;
            $result += ($inputNumber * .5);
            $difference -= ($inputNumber * .5);
        }
        // If possible, multiply by half

        return [
            'mianNumber' => round($mainNumber, 2),
            'inputNumber' => round($inputNumber, 2),
            'result' => $result,
            'multiplications' => $multiplications,
            'difference' => round($difference, 2),
            'dividedInHalf' => $dividedInHalf,
        ];
    }

    public function calculationBasedOnTypeOfDivision(BasicFood $basicFood, $mainNutrition): float|int
    {
        return ($mainNutrition * $basicFood->quantity_per_unit) / 100;
    }


    private function calculateFromMainUnitMultiple($unit, $multiple)
    {
        $dividedInHalf = $multiple['dividedInHalf'];

        if ($unit == 0 || $unit == '') {
            return $unit;
        }
        $multipleNumber = $multiple['multiplications'];
        $result = $unit * $multipleNumber;

        if ($dividedInHalf) {
            $result += ($unit * .5);
        }
        return round($result, 1);


    }

    public function replaceMealListFoods(DietRequestDetail $dietRequestDetail)
    {
        // insert protein
        $conditions = $dietRequestDetail->dietRequest->detail['condition'];
        $meal = $dietRequestDetail->meal;

        // if special meal
        if (self::isSpecialMeal($dietRequestDetail->dietRequest, $meal)) {
            $specialMealsId = $dietRequestDetail->dietRequest->detail[DietRequest::KEY_DETAIL_SPECIAL_BASIC_FOODS][$meal->id];
            if (count($specialMealsId) > 1) {
                unset($specialMealsId[$dietRequestDetail->id]);
            }
            return BasicFood::whereIn('id', $specialMealsId)->get();
        }
        if ($dietRequestDetail->foodable_type == BasicFood::class) {
            $mainNutrition = $dietRequestDetail->main_nutrition;
            return self::getBasicFoods($conditions, $mainNutrition, $meal, $dietRequestDetail->foodClass->id);
        } else {
            return self::getFoods($conditions, $meal, $dietRequestDetail->foodClass->id);
        }

    }

    private function isSpecialMeal(DietRequest $dietRequest, Meal $meal): bool
    {
        if (!isset($dietRequest->detail[DietRequest::KEY_DETAIL_SPECIAL_BASIC_FOODS])){
            return false;
        }
        if (array_key_exists($meal->id, $dietRequest->detail[DietRequest::KEY_DETAIL_SPECIAL_BASIC_FOODS])) {
            return true;
        }
        return false;
    }

    private function getBasicFoods($conditions, $mainNutrition = MainNutritionEnum::protein, $meal = null, $BasicFoodReplacedId = null): \Illuminate\Database\Eloquent\Collection|array
    {
        $basicFoods = BasicFood::query();
        $basicFoods->mainNutritionIs($mainNutrition)->simple();
        $basicFoods = self::handleConditionInModel($basicFoods, $conditions);
        if ($meal) {
            $mealFilter = ($meal instanceof Meal) ? $meal->id : $meal;
            $basicFoods->whereHas('meals', fn($q) => $q->where('id', $mealFilter));
            if ($BasicFoodReplacedId && $basicFoods->count() > 0) {
                $basicFoods->where('id', '!=', $BasicFoodReplacedId);
            }
        }
        return $basicFoods->inRandomOrder()->get();
    }

    public function handleConditionInModel($model, $conditions): mixed
    {
        foreach ($conditions as $key => $value) {
            if ($value == [] || $value == '') {
                continue;
            }
            $model->whereHas('conditions', function ($query) use ($key, $value) {
                $query->where(function ($q) use ($key, $value) {
                    if ($value) {
                        $conditionId = Condition::where('key', $key)->value('id');
                        $value = self::convertArrayValuesToInt($value);
                        $q->where('conditions_pivot.condition_id', $conditionId)
                            ->whereJsonContains('conditions_pivot.options', $value);
                    }
                });
            });
        }
        return $model;
    }

    private function convertArrayValuesToInt($inputArray): array|int|null
    {
        if (!is_array($inputArray)) {
            return ($inputArray === null) ? null : (int)$inputArray;
        }
        // Using array_map to apply intval function to each element in the array
        return array_map(function ($value) {
            // Check if the value is null, and return null if true
            return ($value === null) ? null : intval($value);
        }, $inputArray);
    }

    private function getFoods($conditions, $meal = null, $BasicFoodReplacedId = null): \Illuminate\Database\Eloquent\Collection|array
    {
        $foods = Food::query();
        $foods->whereNull('parent_id');
        $foods = self::handleConditionInModel($foods, $conditions);
        if ($meal) {
            $mealFilter = ($meal instanceof Meal) ? $meal->id : $meal;
            $foods->whereHas('meals', fn($q) => $q->where('id', $mealFilter));
            if ($BasicFoodReplacedId && $foods->count() > 0) {
                $foods->where('id', '!=', $BasicFoodReplacedId);
            }
        }

        return $foods->inRandomOrder()->get();
    }

    public function handleConditionFoodSearch($foodClass, $searchCondition)
    {
        foreach ($searchCondition as $searchConditionKey => $searchConditionValue) {
            if ($searchConditionValue === "") {
                unset($searchCondition[$searchConditionKey]);
            }
        }
        $searchConditions = app('dietService')->wrapAndRemoveNumericKeys($searchCondition);
        return app('dietService')->handleConditionInModel($foodClass, $searchConditions);
    }

    public function wrapAndRemoveNumericKeys($inputArray): array
    {
        return array_map(function ($value) {
            return is_array($value) ? [$value] : [$value];
        }, $inputArray);
    }

    /**
     * @param User $user
     */
    public function insertUserDietPlan(User $user, $dietPlan = null, $dietRequest = null , $targetPlan = null): array
    {
        if ($dietRequest) {
            $detail = $dietRequest->detail;
        }
        $conditions = app('dietService')->getUserConditions($user);

        // insert diet plan id and target plan in condition
        if (isset($targetPlan)){
            $conditions[ConditionKeyEnum::DIET_TYPE->value] = [$targetPlan];
        }
//        if (isset($dietPlan)){
//            $conditions[ConditionKeyEnum::DIET_PATTERN->value] = [$dietPlan->id];
//        }


        // handle plan condition
        if ($dietPlan) {
            $conditionPlan = $dietPlan->conditions()
                ->where('key', ConditionKeyEnum::DIET_PATTERN->value)
                ->first();

            if ($conditionPlan && $conditionPlan->pivot) {
                $options = json_decode($conditionPlan->pivot->options, true);

                if (!empty($options) && is_array($options)) {
                    $conditions[ConditionKeyEnum::DIET_PATTERN->value] = $options;
                }
            }
        }



        $userInformation = app('dietService')->getUserInformation($user);
        if ($dietPlan === null) {
            $dietPlans = app('dietService')->getDietPlans($conditions);

            if (!$dietPlans->count()) {
                $errors = [
                    'errors' => [
                        'هچی پلنی با توجه به اطلاعات وارد شده برای این کاربر یافت نشد'
                    ],
                    'error_type' => 'diet_plan',
                    'count' => 1
                ];
                $detail[DietRequest::KEY_DETAIL_REPORT_MAKE_DIET] = $errors;
                $detail['condition'] = $conditions;
                $dietRequest = $user->dietRequests()->create([
                    'diet_plan_id' => null,
                    'status' => DietRequestStatusEnum::REJECTED,
                    'calories' => 0,
                    'detail' => $detail,
                    'active' => false,
                    'user_information' => $userInformation,
                ]);

                return [
                    'status' => false,
                    'dietRequestModel' => $dietRequest
                ];
            }
            $dietPlan = $dietPlans->random()->first();
        }
        $reduceCalorie = $dietPlan->reduced_calories ?? 0;

        $computedCalorie = app('dietService')->computeCalorie($user, $reduceCalorie , $targetPlan);

        $dietPlanId = ($dietPlan == null ? null : $dietPlan->id);

        $detail['condition'] = $conditions;
        $detail['target_plan'] = $targetPlan;
        $detail['caloriesAndUnits'] = $computedCalorie;


        if ($dietRequest) {
            $newDetail = $detail;
            $dietRequest->update([
                'diet_plan_id' => $dietPlanId,
                'status' => DietRequestStatusEnum::REQUESTED,
                'calories' => $computedCalorie->calorie,
                'detail' => $newDetail,
                'active' => false,
            ]);
        } else {
            $dietRequest = $user->dietRequests()->create([
                'diet_plan_id' => $dietPlanId,
                'status' => DietRequestStatusEnum::REQUESTED,
                'calories' => $computedCalorie->calorie,
                'detail' => $detail,
                'active' => false,
                'user_information' => $userInformation,
            ]);

            $user->dietRequests()->whereIn('status' , [DietRequestStatusEnum::REQUESTED , DietRequestStatusEnum::ACTIVE ])->update([
                'status' => DietRequestStatusEnum::REJECT_BY_SYSTEM,
                'active' => 0
            ]);
        }

        //  Disable old reque


        return [
            'status' => true,
            'dietRequestModel' => $dietRequest
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserConditions(User $user): array
    {
        $conditions = [];
        if ($user->diseases->count()) {
            $conditions[ConditionKeyEnum::DISEASE->value] = $user->diseases->pluck('id')->toArray();
        }
//        if ($user->bodyPhysicalStyle !== null) {
//            $conditions[ConditionKeyEnum::BODY_STYLE->value] = [(int)$user->bodyPhysicalStyle];
//        }

//        if ((int)$user->target_plan == 10) {
//            $conditions[ConditionKeyEnum::ATHLETE_TYPE->value] = [1];
//        } else {
//            $conditions[ConditionKeyEnum::ATHLETE_TYPE->value] = [0];
//        }

        if ($user->foodRestriction) {
            $conditions[ConditionKeyEnum::FOOD_RESTRICTION->value] = $this->arrayChanges($user->foodRestriction);
        }

        if ($user->diet_plan !== null) {
            $conditions[ConditionKeyEnum::DIET_TYPE->value] = [(int)$user->dietPlan];
        }
        $conditions[ConditionKeyEnum::SEASON->value] = [getCurrentSeason()];
        return $conditions;
    }

    private function arrayChanges($string)
    {
        if (Str::startsWith($string, '[') && Str::endsWith($string, ']')) {
            // Remove the '[' and ']' characters
            $trimmedString = Str::replaceFirst('[', '', Str::replaceLast(']', '', $string));

            if (!empty($trimmedString)) {
                // Convert the trimmed string to an array using ',' as the delimiter
                $array = explode(',', $trimmedString);

                // Trim and cast each element of the array to an integer
                $array = array_map('intval', array_map('trim', $array));

                return $array;
            } else {
                // If the trimmed string is empty, return null
                return [];
            }
        }
        return $string;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserInformation(User $user): array
    {
        $userInformation = [];
        foreach (UserMetaEnum::keys() as $key) {
            if (in_array($key, [UserMetaEnum::AVATAR, UserMetaEnum::CREATOR])) {
                continue;
            }
            if ($key->name == 'DISEASES') {
                $userInformation[$key->name] = $user->diseases->pluck('id')->toArray();
            } else {
                $userInformation[$key->name] = $user->{$key->name};
            }
        }
        return $userInformation;
    }

    /**
     * @param $conditions
     * @return Collection|array
     */
    public function getDietPlans($conditions): \Illuminate\Database\Eloquent\Collection|array
    {
        $dietPlans = DietPlan::query();
        $dietPlans = self::handleConditionInModel($dietPlans, $conditions);
        $dietPlans = $dietPlans->where('general_pattern', 0);
        return $dietPlans->inRandomOrder()->get();
    }

    public function userActiveDiet($user)
    {
        return $user->dietRequests()
            ->whereNotNull('diet_plan_id')
            ->where('active', 1)
            ->where('start_date', '<', Carbon::now()->addDay())
            ->where('end_date', '>', Carbon::now())
            ->where('status', DietRequestStatusEnum::ACTIVE)
            ->orderByDesc('id')
            ->first();
    }

    public function userActivePackage(User $user)
    {
        return $user->packages()
            ->where('type', 2)
            ->where('start_date', '<', Carbon::now()->addDay())
            ->where('end_date', '>', Carbon::now())
            ->first();
    }

    public function computeCalorie(User $user, $reducedCalories = 0 , $targetPlan = null , $computeWithLastDiet = false)
    {
        $result['status'] = true;
        if (!$user) {
            $result['status'] = false;
            $result['message'] = 'کاربر یافت نشد';
        }
        $gender = $user->gender;
        if (!isset($gender) || $gender === "") {
            $result['status'] = false;
            $result['message'] = 'جنسیت وارد نشده';
        }
        $weight = $this->getCurrentWeight($user);
        if (!$weight) {
            $result['status'] = false;
            $result['message'] = 'وزن وارد نشده';
        }

        $tall = $user->tall;
        if (!$tall) {
            $result['status'] = false;
            $result['message'] = 'قد وارد نشده';
        }
        $activity = (int)$user->activityPerWeek;


        if ($result['status'] === false) {
            $jsonString = json_encode($result);
            return json_decode($jsonString);
        }
        $BMI = round((($weight / ($tall * $tall)) * 10000));
        $result['BMI'] = $BMI;

        $result = array_merge($result, match (true) {
            $BMI <= 18.5 => ['bodyType' => 'کم وزنی', 'bodyTypeNumber' => 1],
            $BMI <= 24.9 => ['bodyType' => 'طبیعی', 'bodyTypeNumber' => 2],
            $BMI <= 29.9 => ['bodyType' => 'اضافه وزن', 'bodyTypeNumber' => 3],
            $BMI <= 34.9 => ['bodyType' => 'چاقی درجه I', 'bodyTypeNumber' => 4],
            $BMI <= 39.9 => ['bodyType' => 'چاقی درجه II', 'bodyTypeNumber' => 5],
            default => ['bodyType' => 'چاقی درجه III', 'bodyTypeNumber' => 6],
        });
        $IBW = match ($gender) {
            '0' => 48 + ($tall - 150),
            '1' => 45 + ($tall - 150),
            default => null,
        };
        $IBW = round($IBW);

        if ($BMI >= 30 && $BMI < 35){
            $weight = $IBW + 0.4 * ($weight - $IBW);
        }


        $result['weightStatus'] = match (true) {
            $weight >= $IBW => [
                'UnderweightOrOverweight' => 1,
                'overWeight' => round($weight - $IBW),
                'currentWeight' => round($weight - ($weight - $IBW)),
                'underWeight' => null,
            ],
            default => [
                'UnderweightOrOverweight' => 2,
                'overWeight' => null,
                'currentWeight' => round($IBW),
                'underWeight' => round($IBW - $weight),
            ],
        };
        $D_IBW = round(($weight / $IBW) * 100);


        $newResult = [
            'bodyFatStatus' => match (true) {
                $D_IBW >= 200 => 'چاقی شدید',
                ($D_IBW > 130 && $D_IBW < 199) => 'چاقی',
                ($D_IBW > 110 && $D_IBW < 129) => 'اضافه وزن',
                ($D_IBW > 91 && $D_IBW < 109) => 'طبیعی',
                ($D_IBW > 80 && $D_IBW < 90) => 'کمبود وزن',
                ($D_IBW > 70 && $D_IBW < 79) => 'کمبود وزن زیاد',
                default => 'سوء تغذیه شدید',
            }
        ];

        $result = array_merge($result, $newResult);

        $age = $user->age();

        $athleteOrNot = $user->athleteOrNot;
        if (isset($athleteOrNot) && $athleteOrNot == '1') {
            // sport
            $BEE = match ($gender) {
                '0' => round((13.75 * $weight) + (5 * $tall) - (6.75 * $age) + 66.5),
                '1' => round((9.56 * $weight) + (1.85 * $tall) - (4.68 * $age) + 655),
            };
        } else {
            $BEE = match ($gender) {
                '0' => round((10 * $weight) + (6.25 * $tall) - (5 * $age) + 5),
                '1' => round((10 * $weight) + (6.25 * $tall) - (5 * $age) - 161),
            };
        }

//        $calorieActivity = $BEE * 0.3; // Equivalent to $BEE = $BEE - ((10 * $BEE) / 100);

        $calorie = $BEE;
        if ($activity === null) {
            $calorie *= 1.2;
        } else {
            if (isset($athleteOrNot) && $athleteOrNot == '1') {
                $multiplier = match ($activity) {
                    0 => 1.35,
                    1 => 1.45,
                    2 => 1.6,
                    3 => 1.8,
                    4 => 2.2,
                    default => 1.45,
                };
            } else {
                $multiplier = match ($activity) {
                    0 => 1.2,
                    1 => 1.35,
                    2 => 1.45,
                    3 => 1.6,
                    4 => 1.9,
                    default => 1.35,
                };
            }


            $calorie *= $multiplier;
        }
        $calorie = round($calorie);


        $baseCalorie = max($calorie, 1000);
        $result['base_calorie'] = round($baseCalorie);

//        $calorie -= ($BEE < 1500) ? 500 : 800;

        $calorieAdjustment = 500;
        if (isset($user->weightChangePerWeek)){
            $weightChangePerWeek = (int)$user->weightChangePerWeek;
           if ($weightChangePerWeek === 1) {
               $calorieAdjustment = 800;
           } elseif ($weightChangePerWeek === 2) {
               $calorieAdjustment = 1200;
           }
        }

        //target plan to gain or lose or stop weigh for example lose weight
        if ($targetPlan !== null){
            $handleTargetPlan = (int) $targetPlan;
        } else {
            if ($user->dietPlan !== null){
                $handleTargetPlan = (int) $user->dietPlan;
            } else {
                $handleTargetPlan = null;
            }
        }

        if (isset($handleTargetPlan)) {
            if ($handleTargetPlan == 0) {
                $calorie -= $calorieAdjustment;
            } elseif ($handleTargetPlan == 1) {
                $calorie += $calorieAdjustment;
            }
        } else {
            if ($weight > $IBW) {
                $calorie -= $calorieAdjustment;
            } elseif ($weight < $IBW) {
                $calorie += $calorieAdjustment;
            }
        }

        $calorie -= $reducedCalories;
        $calorie = max($calorie, 1000);

        if($computeWithLastDiet && $user->activeDiet()){
            $calorie = $user->activeDiet()->calories;
        }

        // calculate protein
        $basicProtein = 2;

        $proteinPerUser = $weight * $basicProtein;
//        $percentProtein = (($proteinPerUser * 4) / $baseCalorie) * 100;
        $result['body_physical_style'] = '';
        if (isset($user->bodyPhysicalStyle)) {
            $result['body_physical_style'] = $user->bodyPhysicalStyle;
            $bodyPhysicalStyle = $user->bodyPhysicalStyle;
            if ($bodyPhysicalStyle == 0) {
                $result['unitsNeeded']['protein'] = round((.25 * $calorie) / 4);
                $result['unitsNeeded']['carb'] = round((.55 * $calorie) / 4);
                $result['unitsNeeded']['fat'] = round((.2 * $calorie) / 9);
            } elseif ($bodyPhysicalStyle == 1) {
                $result['unitsNeeded']['protein'] = round((.3 * $calorie) / 4);
                $result['unitsNeeded']['carb'] = round((.4 * $calorie) / 4);
                $result['unitsNeeded']['fat'] = round((.3 * $calorie) / 9);
            } else {
                $result['unitsNeeded']['protein'] = round((.35 * $calorie) / 4);
                $result['unitsNeeded']['carb'] = round((.25 * $calorie) / 4);
                $result['unitsNeeded']['fat'] = round((.4 * $calorie) / 9);
            }
        } else {
            $result['unitsNeeded']['protein'] = $proteinPerUser;
            $result['unitsNeeded']['carb'] = round((.5 * $calorie) / 4);
            $result['unitsNeeded']['fat'] = round((0.25 * $calorie) / 9);
        }
        $result['unitsNeeded']['fiber'] = $this->computeFiberNeed($user);

        // calculate protein

        $result['status'] = true;
        $result['calorie'] = round($calorie);

        $jsonString = json_encode($result);
        return json_decode($jsonString);
    }

    public function getCurrentWeight(User $user)
    {
        return $user->current_weight > 0 ? $user->current_weight : $user->weight;
    }

    public function getStartWeight(User $user)
    {
        $weights = $user->weightMeta;

        if ($weights && $weights->isNotEmpty()) {
            // Sort the collection by creation date to find the first entered weight
            $firstWeightMeta = $weights->sortBy('created_at')->first();

            return (float) $firstWeightMeta->meta_value; // Return the first weight as a float
        }

        // Return null if no weights are found
        return null;
    }

    private function computeFiberNeed(User $user): int
    {
        $gender = (string)$user->gender;
        $age = $user->age();
        if ($gender == '0') {
            // Only for men
            if ($age <= 3) {
                return 19;
            } elseif ($age <= 8) {
                return 25;
            } elseif ($age <= 13) {
                return 31;
            } elseif ($age <= 19) {
                return 38;
            } elseif ($age <= 50) {
                return 33;
            } else {
                return 28;
            }
        } else {
            // Only for women
            if ($age <= 3) {
                return 19;
            } elseif ($age <= 8) {
                return 25;
            } elseif ($age <= 19) {
                return 26;
            } elseif ($age <= 50) {
                return 27;
            } else {
                return 22;
            }
        }
        // only women
    }

    public function getUserPackages()
    {

    }

    /**
     * @param $model
     * @param $conditions
     * @return mixed
     */

    public function getListBasicFoods($conditions)
    {
        $basicFoods = BasicFood::query();
        $basicFoods = self::handleConditionInModel($basicFoods, $conditions);
        return $basicFoods->inRandomOrder()->get();
    }

    /**
     * @param $conditions
     * @return mixed
     */
    public function getListFoods($conditions)
    {
        $foods = Food::query();
        $foods = self::handleConditionInModel($foods, $conditions);
        return $foods->inRandomOrder()->get();
    }

    public function makeRejim(DietRequest $dietRequest, $actionRequest)
    {

        // When the amount of the diet_lan is empty
        if ($dietRequest->dietPlan == null) {
            $errors = [
                'errors' => [
                    'هچی پلنی با توجه به اطلاعات وارد شده برای این کاربر یافت نشد'
                ],
                'error_type' => 'diet_plan',
                'count' => 1
            ];
            $detail = $dietRequest->detail;
            $detail[DietRequest::KEY_DETAIL_REPORT_MAKE_DIET] = $errors;
            $dietRequest->update($detail);
            return $errors;
        }
        // When the amount of the diet_lan is empty

        $condition['status'] = DietRequestStatusEnum::PENDING;
        $condition['active'] = false;
        $dietRequest->update($condition);
        MakeRejimJob::dispatch($dietRequest, $actionRequest);


    }

    public function resultFilterAndDisplayErrors($data): array
    {
        $filteredArray = [];
        $count = 0;

        // Iterate through the main array
        if (isset($data['error_type']) && $data['error_type'] == 'diet_plan') {
            $filteredArray[] = [
                'error' => 'هیچ پلنی یافت نشد'
            ];
            return [
                'errors' => $filteredArray,
                'error_type' => 'request_plan',
                'count' => 1,
            ];
        }
        foreach ($data['result'] as $day => $value) {
            foreach ($value as $unit => $units) {
                if (is_array($units)) {
                    if (!isset($units['food_id'])) {
                        $count++;
                        $filteredArray[] = [
                            'error' => ' غذایی برای روز شماره ' . $day . ' برای بخش  وعده ' . $units['meal'] . ' یافت نشد ',
                            'day' => $day,
                            'unit' => $units['meal'],
                            'meal' => [
                                'name' => $units['meal'],
                                'id' => $units['meal_id']
                            ]
                        ];
                    }
                }
            }
        }
        return [
            'errors' => $filteredArray,
            'error_type' => 'request_detail',
            'count' => $count,
        ];
    }

    public function getBasicFoodsSpecial($meal = null, $foodType = FoodTypeEnum::SIMPLE->value): \Illuminate\Database\Eloquent\Collection|array
    {
        $foods = Food::whereNull('parent_id');
        if ($meal) {
            $mealFilter = ($meal instanceof Meal) ? $meal->id : $meal;
            $foods->whereHas('meals', fn($q) => $q->where('id', $mealFilter));
        }
        return $foods->orderByDesc('id')->get();
    }

    function countDoubling($a, $b)
    {
        // بررسی اینکه اگر a برابر با b بود
        if ($a == $b) {
            return 1;
        }

        // تقسیم عدد دوم بر عدد اول
        return $b / $a;
    }


    public function generateFoodCombinations(Food $food, $stepLimit = null)
    {
        $basicFoods = $food->basicFoodPivot;

        if ($basicFoods->isEmpty()) {
            return [];
        }

        $combinations = [[]];

        foreach ($basicFoods as $basicFoodPivot) {
            $newCombinations = [];
            $initialQuantity = $basicFoodPivot->pivot->quantity;
            $maxQuantity = $basicFoodPivot->pivot->maximum ?? ($initialQuantity * 10);

            if ($initialQuantity > $maxQuantity) {
                $maxQuantity = $initialQuantity;
            }

            $step = $initialQuantity < 1 ? $initialQuantity : 1;

            for ($quantity = $initialQuantity; $quantity <= $maxQuantity; $quantity += $step) {
                foreach ($combinations as $combination) {
                    $newCombination = $combination;

                    $newCombination[] = [
                        'basic_food_id' => $basicFoodPivot->id,
                        'init' => $initialQuantity ?? null,
                        'diff' => round($quantity / $initialQuantity, 2), // چندبرابری مقدار
                        'unit' => round($quantity, 2),
                    ];

                    $newCombinations[] = $newCombination;
                }
            }

            $combinations = $newCombinations;
        }

        // اعمال فیلتر برای حذف ردیف‌هایی که اختلاف‌شان بیشتر از $stepLimit است
        if ($stepLimit !== null) {
            $combinations = array_filter($combinations, function ($combination) use ($stepLimit) {
                $diffs = array_column($combination, 'diff');
                $maxDiff = max($diffs);
                $minDiff = min($diffs);
                return ($maxDiff - $minDiff) <= $stepLimit; // بررسی اختلاف
            });
        }

        // مرتب‌سازی نهایی
        return $this->sortCombinations($combinations);
    }

    private function sortCombinations(array $combinations): array
    {
        usort($combinations, function ($a, $b) {
            for ($i = 0; $i < count($a); $i++) {
                if ($a[$i]['unit'] < $b[$i]['unit']) {
                    return -1;
                } elseif ($a[$i]['unit'] > $b[$i]['unit']) {
                    return 1;
                }
            }
            return 0;
        });

        return $combinations;
    }

    public function generateFoodVariantsList(Food $food, int $stepLimit = 10): array
    {
        $maxRepeat = 10;
        $variants = []; // لیست نهایی ترکیبات غذا
        $basicFoodPivots = $food->basicFoodPivot; // غذاهای پایه مرتبط با این غذا

        if ($basicFoodPivots->isEmpty()) {
            return []; // اگر غذاهای پایه تعریف نشده باشد
        }

        // آرایه‌ای از حالت‌های اولیه برای هر غذای پایه
        $combinations = [[]];

        // گرفتن غذای اصلی که تیک extend داره
        $mainFoodPivot = $basicFoodPivots->firstWhere('pivot.extend', 1);
        $mainMaxAllowed = $mainFoodPivot ? $mainFoodPivot->pivot->maximum ?? $maxRepeat : $maxRepeat;

        if (!$mainFoodPivot) {
            $mainFoodPivot = $basicFoodPivots->first();
        }

        foreach ($basicFoodPivots as $basicFoodPivot) {
            $basicFood = BasicFood::find($basicFoodPivot->pivot->basic_food_id);
            $initialQuantity = $basicFoodPivot->pivot->quantity; // مقدار اولیه
            $maxAllowed = $basicFoodPivot->pivot->maximum ?? $this->countDoubling($mainFoodPivot->pivot->quantity, $mainMaxAllowed) * $initialQuantity ?? ($initialQuantity * $maxRepeat); // حداکثر مقدار (اگر محدودیت نباشد از غذای اصلی استفاده می‌کنیم)
            $extendEnabled = $basicFoodPivot->pivot->extend == 1; // بررسی "تیک" گسترش

            $integration = $this->countDoubling($mainFoodPivot->pivot->quantity, $maxAllowed) ?? $maxAllowed;
            $newCombinations = [];


            for ($i = 1; $i <= $integration; $i++) {
                foreach ($combinations as $combination) {
                    $currentAmount = $i * $initialQuantity;
                    if ($currentAmount > $maxAllowed) {
                        continue; // از مقدار حداکثر عبور کرده است
                    }

                    $doubling = $this->countDoubling($initialQuantity, $currentAmount);

                    $newCombination = $combination;
                    $newCombination[] = [
                        'basic_food_id' => $basicFood->id,
                        'doubling' => $doubling,
                        'unit' => $currentAmount,
                        'max' => $currentAmount,
                    ];

                    // بررسی تفاوت doubling بین ایتم‌ها
                    $valid = true;
                    if ($stepLimit !== null) {
                        foreach ($newCombination as $existingItem) {
                            if (isset($existingItem['doubling']) && abs($doubling - $existingItem['doubling']) > $stepLimit) {
                                $valid = false; // اگر اختلاف بیشتر از stepLimit باشد، این ایتم نامعتبر است
                                break;
                            }
                        }
                    }

                    if ($valid) {
                        $newCombinations[] = $newCombination; // اضافه کردن ترکیب به آرایه اگر اختلاف معتبر باشد
                    }
                }
            }

            // به روز رسانی آرایه ترکیبات با ترکیبات جدید
            $combinations = $newCombinations;
        }

        // در نهایت تمام ترکیبات نهایی را برمی‌گردانیم
        return $combinations;
    }



    public function makeDietListSample(Food $food): array
    {
        $iteration = 20;
        $result = [];
        $unitsCache = [];

        for ($i = 1; $i <= $iteration; $i++) {
            $status = true;
            $body = [];

            $extend = [];
            $mood = 1;
            foreach ($food->basicFoodPivot as $basicFood) {

                $primaryUnit = $unitsCache[$basicFood->id]['primary'] ??= $basicFood->units()->where('is_primary', '1')->first();
                $fallbackUnit = $unitsCache[$basicFood->id]['fallback'] ??= $basicFood->units()->first();

                $unitName = $primaryUnit?->name ?? $fallbackUnit->name;


                if (isset($basicFood->pivot->maximum) && $basicFood->pivot->maximum > 0 && $basicFood->pivot->maximum < ($basicFood->pivot->quantity * $i)) {
                    $status = false;
                    $body[] = [
                        'is_done' => true,
                        'food_name' => $basicFood->name,
                        'body' => 'حداکثر مقدار ' . $basicFood->pivot->maximum . ' ' . $unitName . ' میباشد',
                    ];
                    break;
                } else {
                    $unit = $basicFood->pivot->quantity * $i;
                    $foodUnit = $basicFood->units()->where('is_primary', true)->first() ?? $basicFood->units()->first();
                    $quantity_per_unit = $foodUnit->pivot->quantity_per_unit;
                    $caloriePerUnit = (($quantity_per_unit * $unit * $basicFood->food_fact['CALORIE']) / 100);
                    $proteinPerUnit = (($quantity_per_unit * $unit * $basicFood->food_fact['PROTEIN']) / 100);
                    $carbPerUnit = (($quantity_per_unit * $unit * $basicFood->food_fact['CARBOHYDRATE']) / 100);
                    $fiberPerUnit = (($quantity_per_unit * $unit * $basicFood->food_fact['FIBER']) / 100);
                    $fatPerUnit = (($quantity_per_unit * $unit * $basicFood->food_fact['FAT']) / 100);

                    $body[$mood][] = [
                        'id' => $basicFood->pivot->id,
                        'food_name' => $basicFood->name,
                        'food_id' => $basicFood->id,
                        'amount' => $basicFood->pivot->quantity * $i,
                        'next_step' => $basicFood->pivot->quantity * ($i + 1),
                        'unit' => $unitName,
                        'cal' => [
                            'q' => $basicFood->pivot->quantity,
                            'i' => $i,
                            'cal' => $basicFood->pivot->quantity * $i,
                        ],
                        'max' => $basicFood->pivot->maximum,
                        'extend' => $basicFood->pivot->extend == '1',
                        'body' => ($basicFood->pivot->quantity * $i) . ' ' . $unitName,
                        'calculateFact' => [
                            'calorie' => $caloriePerUnit,
                            'protein' => $proteinPerUnit,
                            'carb' => $carbPerUnit,
                            'fiber' => $fiberPerUnit,
                            'fat' => $fatPerUnit,
                        ]
                    ];
                    if ($basicFood->pivot->extend == '1') {
                        $extend[] = $basicFood->pivot->id;
                    }
                }

            }
            if ($extend) {
                $body = $this->extendArray($body, $extend);
            }

            $result[$i] = [
                'status' => $status,
                'body' => $body,
            ];

            if (!$status) {
                break;
            }
        }

        return $result;
    }

    private function extendArray($array, $extendList)
    {
        if (!count($array)) {
            return $array;
        }
        $mainArray = $array[1];

        // Use array_filter to find the array with the specified ID
        $lastmoodId = 1;
        foreach ($extendList as $extend) {
            $filteredArray = array_filter($array[1], function ($item) use ($extend) {
                return $item['id'] == $extend;
            });

            // Reset the array keys and get the first result if exists
            $arrayKeySelected = key($filteredArray);
            $filteredArray = array_values($filteredArray)[0];


            $AmountToBeAdded = 1;
            $repetitions = (float)$filteredArray['next_step'] - (float)$filteredArray['amount'];
            if ($repetitions < 1 && $repetitions > 0) {
                $AmountToBeAdded = $repetitions;
                $repetitions = round(1 / $repetitions);
            }
            if ($repetitions > 0) {

                $nextStep = $filteredArray['next_step'];
                if ($nextStep < 1) {
                    $nextStep = 1;
                }
                for ($i = 1; $i < $repetitions; $i++) {

                    $amount = $filteredArray['amount'] + ($i * $AmountToBeAdded);
                    $array[$i + $lastmoodId] = $mainArray;
                    $array[$i + $lastmoodId][$arrayKeySelected] = [
                        'id' => $filteredArray['id'],
                        'food_name' => $filteredArray['food_name'],
                        'food_id' => $filteredArray['food_id'],
                        'amount' => $amount,
                        'next_step' => $nextStep,
                        'unit' => $filteredArray['unit'],
                        'max' => $filteredArray['max'],
                        'extend' => $filteredArray['extend'],
                        'body' => $amount . ' ' . $filteredArray['unit'],
                    ];
                    $lastmoodId++;
                }

            }

        }

        return $array;

    }

    public function complexFoodResult($detail): array
    {
        //collection of the basic food that create a complex food ;
        $basicFoodCollection = $detail->basicFoodPivot;
        $returnValue = [];
        if ($basicFoodCollection->count() > 0) {
            foreach ($basicFoodCollection as $key => $basicFood) {
                $unit = $basicFood->units()->where('is_primary', '1')->first()?->name ?? $basicFood->units()->first()->name;
                $returnValue[$key] = '<span class="avatar avatar-sm bradius bg-info">'.$basicFood->pivot->quantity.'</span> <span class="badge bg-default my-1">' . ' ' . $unit . ' </span>  <span class="badge bg-default my-1">' . $basicFood->name .'</span>';
            }
        }
        return $returnValue;
    }

    public function makeDietFromBasicFoods(DietRequest $dietRequest, $actionRequest)
    {
        $conditions = $dietRequest->detail['condition'] ?? app('dietService')->getUserConditions($dietRequest->user);
        // create protein list foods

        if ($actionRequest == DietRequest::ACTION_REQUEST_RE_GENERATE) {
            DietRequestDetail::where('diet_request_id', $dietRequest->id)->delete();
        }

        // check have handwritten and insert
        if (isset($dietRequest->dietPlan->detail['handwritten']) || isset($dietRequest->dietPlan->detail['special_basic_foods'])) {
            $dietRequestDetail = $dietRequest->detail;

            // insert handwritten
            if (isset($dietRequest->dietPlan->detail['handwritten'])) {
                $dietRequestDetail[DietRequest::KEY_DETAIL_HANDWRITTEN] = $dietRequest->dietPlan->detail['handwritten'];
            }

            // insert special meals
            if (isset($dietRequest->dietPlan->detail['special_basic_foods'])) {
                $dietRequestDetail[DietRequest::KEY_DETAIL_SPECIAL_BASIC_FOODS] = $dietRequest->dietPlan->detail['special_basic_foods'];
            }

            $dietRequest->update([
                'detail' => $dietRequestDetail
            ]);
        }

        // diet from calorie [combined]
        if ($dietRequest->dietPlan->food_type == FoodTypeEnum::COMBINED) {
//            $minCalorie = $dietRequest->detail['caloriesAndUnits']['calorie'];
            $foods = self::getFoods( conditions: $conditions  );
            dd($foods);
            return $this->foodArrangement($foods, $dietRequest, null, [], $actionRequest);

        } else {

            // insert protein
            $mainNutrition = MainNutritionEnum::protein;
            $basicFoods = self::getBasicFoods($conditions, $mainNutrition);
            $resultMakeDiet = $this->basicFoodArrangement($basicFoods, $dietRequest, $mainNutrition, [], $actionRequest);
            // insert protein

            // insert carb
            $mainNutrition = MainNutritionEnum::carb;
            $basicFoods = self::getBasicFoods($conditions, $mainNutrition);
            $resultMakeDiet = $this->basicFoodArrangement($basicFoods, $dietRequest, $mainNutrition, $resultMakeDiet, $actionRequest);
            // insert carb

            // insert fat
            $mainNutrition = MainNutritionEnum::fat;
            $basicFoods = self::getBasicFoods($conditions, $mainNutrition);
            $resultMakeDiet = $this->basicFoodArrangement($basicFoods, $dietRequest, $mainNutrition, $resultMakeDiet, $actionRequest);
            // insert fat

            // insert fiber
            $mainNutrition = MainNutritionEnum::fiber;
            $basicFoods = self::getBasicFoods($conditions, $mainNutrition);
            // insert fiber

            return $this->basicFoodArrangement($basicFoods, $dietRequest, $mainNutrition, $resultMakeDiet, $actionRequest);
        }


    }

    private function foodArrangement($foods, $dietRequest, $resultMakeDiet, $actionRequest)
    {
        $foodsList = [];
        $resultMakeDiet = $resultMakeDiet ?? [];
        $mainFoods = clone $foods;
        $i = 0;
        do {
            $i++;
            if ($i == 1) {
                $foodsList = self::createDietFoodsWithUnits($dietRequest, $foods, $i, $mainFoods, $resultMakeDiet, $actionRequest);
                $resultMakeDiet = $foodsList['resultMakeDiet'];
            } else {
                $basicFoods = (count($foodsList['basicFoods']) >= $foodsList['mealCount']) ? $foodsList['basicFoods'] : $foods;
                $foodsList = self::createDietFoodsWithUnits($dietRequest, $foods, $i, $mainFoods, $resultMakeDiet, $actionRequest);
                $resultMakeDiet = $foodsList['resultMakeDiet'];
            }

        } while ($dietRequest->dietPlan->day_count > $i);


        return $resultMakeDiet;
        //list protein foods
    }

    private function createDietFoodsWithUnits($dietRequest, $foods, $dayNumber, $mainFoods, $resultMakeDiet, $actionRequest): array
    {
        $meals = $dietRequest->dietPlan->meals()->orderBy('priority')->get();

        $totalAmountPerUnit = $dietRequest->detail['caloriesAndUnits']['calorie'];
        $foodsMeals = clone $foods;
        $amountDifference = 0;
        $mealsDataInserted = [];
        foreach ($meals as $meal) {

            // check special food for this meal
            if (self::isSpecialMeal($dietRequest, $meal)) {
                $specialMealsId = $dietRequest->detail[DietRequest::KEY_DETAIL_SPECIAL_BASIC_FOODS][$meal->id];
                $foodsMeals = Food::whereIn('id', $specialMealsId)->get();
            }


            // check exist in db
            if ($actionRequest == DietRequest::ACTION_REQUEST_UPDATE_LIST) {
                $checkExists = DietRequestDetail::where([
                    ['meal_id', $meal->id],
                    ['diet_request_id', $dietRequest->id],
                    ['day_number', $dayNumber],
                ]);
                if ($checkExists->exists()) {
                    continue;
                }
            }

            $baseAmountNeed = $amountPerUnit = 0;

            // filter selected repetitive foods
            $foodsMeals = self::filterSelectedRepetitive($dietRequest, $meal, $foodsMeals, $mainFoods);

            //To apply the minimum and maximum prescribed amount
            if (!self::isSpecialMeal($dietRequest, $meal)) {
                $amountPerUnit = $meal->pivot->calorie_percent ?? 100;
                $amountNeed = round($totalAmountPerUnit * ($amountPerUnit / 100), 1);

                // Limit the maximum number of calories needed
                $foodsMeals = $foodsMeals->filter(function ($food) use ($amountNeed) {
                    if ($food->max_per_unit == null) {
                        return true;
                    }
                    return $food->calories * $food->max_per_unit > $amountNeed;
                });

                // Filter out foods that have fewer calories than the minimum requirement
                $foodsMeals = $foodsMeals->filter(function ($food) use ($amountNeed) {
                    return $food->calories < $amountNeed;
                });


            }

            $foodSelected = null;
            $foodUnitMultiple = [];
            if ($foodsMeals->count()) {
                $foodSelected = $foodsMeals->random();
                // when special food selected
                if (self::isSpecialMeal($dietRequest, $meal)) {
                    // It should only be saved once for special foods
                    if (!$foodSelected->dietRequestDetails()->where([
                        ['day_number', $dayNumber],
                        ['meal_id', $meal->id],
                        ['diet_request_id', $dietRequest->id],
                    ])->exists()) {
                        $modelCreate = [
                            'diet_request_id' => $dietRequest->id,
                            'meal_id' => $meal->id,
                            'calories' => $foodSelected->calories,
                            'day_number' => $dayNumber,
                            'calculated_value' => 1,
                            'number_of_unit' => 1,
                            'main_nutrition' => MainNutritionEnum::special,
                            'date_of_day' => Carbon::now()->addDays($dayNumber)->toDateString(),
                        ];
                        $foodSelected->dietRequestDetails()->create($modelCreate);
                        $amountDifference = 1;
                    }
                    continue;
                } // insert meal nutrition
                else {
                    if ($meal->pivot->calorie_percent == 0) {
                        continue;
                    }
                    $baseAmountNeed = $amountNeed;
                    if ($amountDifference > 0) {
                        $amountNeed += $amountDifference;
                    }

                    $foodUnitMultiple = self::findNearestSmallerMultiple($amountNeed, $foodSelected->calories);
                    $amountDifference = $foodUnitMultiple['difference'] ?? 0;

                    $numberOfUnit = $foodUnitMultiple['dividedInHalf'] ? $foodUnitMultiple['multiplications'] + 0.5 : $foodUnitMultiple['multiplications'];

                    $modelCreate = [
                        'diet_request_id' => $dietRequest->id,
                        'meal_id' => $meal->id,
                        'calories' => (float)self::calculateFromMainUnitMultiple($foodSelected->calories, $foodUnitMultiple),
                        'day_number' => $dayNumber,
                        'calculated_value' => $amountNeed,
                        'number_of_unit' => $numberOfUnit,
                        'main_nutrition' => MainNutritionEnum::all,
                        'date_of_day' => Carbon::now()->addDays($dayNumber)->toDateString(),
                    ];
                    foreach (MainNutritionEnum::cases() as $nutritionList) {
                        $modelCreate[$nutritionList->name] = (float)self::calculateFromMainUnitMultiple($foodSelected->{$nutritionList->name}, $foodUnitMultiple);
                    }
                    $foodSelected->dietRequestDetails()->create($modelCreate);
                    $foods = $foods->reject(function ($foods) use ($foodSelected) {
                        return $foods === $foodSelected;
                    });

                }

                $foodsMeals = $foods;
            }
            $mealsDataInserted[] = [
                'id' => $meal->id,
                'food_selected' => $foodSelected?->id,
                'name' => $meal->name,
                'food_unit_multiple' => $foodUnitMultiple ?? '',
                'amount' => [
                    'amount_difference' => $baseAmountNeed > 0 ? round($amountDifference, 1) : null,
                    'base_amount_need' => $baseAmountNeed,
                    'base_amount_need_percent' => $amountPerUnit ? $amountPerUnit / 100 : null,
                ]
            ];

        }
        $resultMakeDiet[$dayNumber]['all'] = [
            'totalAmountPerUnit' => $totalAmountPerUnit,
            'meals' => $mealsDataInserted,
        ];


        return ['basicFoods' => $foods, 'mealCount' => count($meals), 'resultMakeDiet' => $resultMakeDiet];
        // get list protein
    }

    private function filterSelectedRepetitive($dietRequest, $meal, $foodsMeals, $mainFoods)
    {
        if (!self::isSpecialMeal($dietRequest, $meal)) {
            //filter meal
            $foodsMeals = $foodsMeals->filter(function ($food) use ($meal) {
                return in_array($meal->id, $food->meals->pluck('id')->toArray());
            });

            if ($foodsMeals->count() == 0 && $mainFoods->count()) {
                $foodsMeals = clone $mainFoods;
                $foodsMeals = $foodsMeals->filter(function ($food) use ($meal) {
                    return in_array($meal->id, $food->meals->pluck('id')->toArray());
                });
            }
        }
        return $foodsMeals;
    }

    private function basicFoodArrangement($basicFoods, $dietRequest, $mainNutrition, $resultMakeDiet, $actionRequest)
    {
        $basicFoodsList = [];
        $resultMakeDiet = $resultMakeDiet ?? [];
        $mainFoods = clone $basicFoods;
        $i = 0;
        do {
            $i++;
            if ($i == 1) {
                $basicFoodsList = self::createBasicDietFoodsWithUnits($dietRequest, $basicFoods, $i, $mainNutrition, $mainFoods, $resultMakeDiet, $actionRequest);
                $resultMakeDiet = $basicFoodsList['resultMakeDiet'];
            } else {
                $basicFoods = (count($basicFoodsList['basicFoods']) >= $basicFoodsList['mealCount']) ? $basicFoodsList['basicFoods'] : $basicFoods;
                $basicFoodsList = self::createBasicDietFoodsWithUnits($dietRequest, $basicFoods, $i, $mainNutrition, $mainFoods, $resultMakeDiet, $actionRequest);
                $resultMakeDiet = $basicFoodsList['resultMakeDiet'];
            }

        } while ($dietRequest->dietPlan->day_count > $i);


        return $resultMakeDiet;
        //list protein foods
    }

    private function createBasicDietFoodsWithUnits($dietRequest, $basicFoods, $dayNumber, $mainNutrition, $mainFoods, $resultMakeDiet, $actionRequest): array
    {
        $meals = $dietRequest->dietPlan->meals()->orderBy('priority')->get();

        $totalAmountPerUnit = $dietRequest->detail['caloriesAndUnits']['unitsNeeded'][$mainNutrition->name];
        $basicFoodsMeals = clone $basicFoods;
        $amountDifference = 0;
        $mealsDataInserted = [];
        foreach ($meals as $meal) {

            // check special food for this meal
            if (self::isSpecialMeal($dietRequest, $meal)) {
                $specialMealsId = $dietRequest->detail[DietRequest::KEY_DETAIL_SPECIAL_BASIC_FOODS][$meal->id];
                $basicFoodsMeals = BasicFood::whereIn('id', $specialMealsId)->get();
            }


            // check exist in db
            if ($actionRequest == DietRequest::ACTION_REQUEST_UPDATE_LIST) {
                $checkExists = DietRequestDetail::where([
                    ['meal_id', $meal->id],
                    ['diet_request_id', $dietRequest->id],
                    ['day_number', $dayNumber],
                    ['main_nutrition', $mainNutrition->value]
                ]);
                if ($checkExists->exists()) {
                    continue;
                }
            }
            $baseAmountNeed = $amountPerUnit = 0;


            //To apply the minimum and maximum prescribed amount
            $amountNeed = '';
            if (!self::isSpecialMeal($dietRequest, $meal)) {
                $amountPerUnit = $meal->pivot->{$mainNutrition->name} ?? 100;
                $amountNeed = round($totalAmountPerUnit * ($amountPerUnit / 100), 1);
                if ($amountNeed == 0) {
                    continue;
                }


                $basicFoodsMeals = $basicFoodsMeals->filter(function ($food) use ($mainNutrition, $amountNeed) {
                    $foodType = self::calculationBasedOnTypeOfDivision($food, $food->{$mainNutrition->name});
                    return $foodType < $amountNeed;
                });

                // Limit the maximum number of calories needed
                $basicFoodsMeals = $basicFoodsMeals->filter(function ($food) use ($mainNutrition, $amountNeed) {
                    if ($food->max_allowed == null) {
                        return true;
                    }
                    $foodType = self::calculationBasedOnTypeOfDivision($food, $food->{$mainNutrition->name});
                    return $foodType * $food->max_allowed > $amountNeed;
                });
            }
            // filter selected repetitive foods
            $basicFoodsMeals = self::filterSelectedRepetitive($dietRequest, $meal, $basicFoodsMeals, $mainFoods);

            $basicFoodSelected = null;
            $foodUnitMultiple = [];
            $baseAmountNeed = $amountNeed;
            if ($basicFoodsMeals->count()) {
                $basicFoodSelected = $basicFoodsMeals->random();
                // when special food selected
                if (self::isSpecialMeal($dietRequest, $meal)) {

                    // It should only be saved once for special foods
                    if (!$basicFoodSelected->dietRequestDetails()->where([
                        ['day_number', $dayNumber],
                        ['meal_id', $meal->id],
                        ['diet_request_id', $dietRequest->id],
                        ['main_nutrition', MainNutritionEnum::special->name],
                    ])->exists()) {
                        $modelCreate = [
                            'diet_request_id' => $dietRequest->id,
                            'meal_id' => $meal->id,
                            'calories' => $basicFoodSelected->calories,
                            'day_number' => $dayNumber,
                            'calculated_value' => 1,
                            'number_of_unit' => 1,
                            'main_nutrition' => MainNutritionEnum::special,
                            'date_of_day' => Carbon::now()->addDays($dayNumber)->toDateString(),
                        ];
                        $basicFoodSelected->dietRequestDetails()->create($modelCreate);
                        $amountDifference = 1;
                    }
                    continue;
                } // insert meal nutrition
                else {

                    if ($meal->pivot->{$mainNutrition->name} == 0) {
                        continue;
                    }

                    // If at least the required amount could not be provided


                    if ($amountDifference > 0) {
                        $amountNeed += $amountDifference;
                    }


                    $foodUnitMultiple = self::findNearestSmallerMultiple($amountNeed, self::calculationBasedOnTypeOfDivision($basicFoodSelected, $basicFoodSelected->{$mainNutrition->name}));
                    $amountDifference = $foodUnitMultiple['difference'] ?? 0;

                    $numberOfUnit = $foodUnitMultiple['dividedInHalf'] ? $foodUnitMultiple['multiplications'] + 0.5 : $foodUnitMultiple['multiplications'];
                    $quantityPerUnitTotal = ((float)self::calculateFromMainUnitMultiple($basicFoodSelected->quantity_per_unit, $foodUnitMultiple));

                    $modelCreate = [
                        'diet_request_id' => $dietRequest->id,
                        'meal_id' => $meal->id,
                        'calories' => (($basicFoodSelected->calories / 100) * $quantityPerUnitTotal),
                        'day_number' => $dayNumber,
                        'calculated_value' => $amountNeed,
                        'number_of_unit' => $numberOfUnit,
                        'main_nutrition' => $mainNutrition->name,
                        'date_of_day' => Carbon::now()->addDays($dayNumber)->toDateString(),
                    ];
                    foreach (MainNutritionEnum::cases() as $nutritionList) {
                        if ($nutritionList == $mainNutrition) {
                            $modelCreate[$mainNutrition->name] = round($foodUnitMultiple['result'], 1);
                        }
                        $modelCreate[$nutritionList->name] = (($basicFoodSelected->{$nutritionList->name} / 100) * $quantityPerUnitTotal);
                    }

                    $basicFoodSelected->dietRequestDetails()->create($modelCreate);

                    $basicFoods = $basicFoods->reject(function ($basicFoods) use ($basicFoodSelected) {
                        return $basicFoods === $basicFoodSelected;
                    });

                }

                $basicFoodsMeals = $basicFoods;
            }
            $mealsDataInserted[] = [
                'id' => $meal->id,
                'food_selected' => $basicFoodSelected == null ? null : $basicFoodSelected->id,
                'name' => $meal->name,
                'food_unit_multiple' => $foodUnitMultiple ?? '',
                'amount' => [
                    'amount_difference' => $baseAmountNeed > 0 ? round($amountDifference, 1) : null,
                    'base_amount_need' => $baseAmountNeed,
                    'base_amount_need_percent' => $amountPerUnit ? $amountPerUnit / 100 : null,
                ]
            ];

        }
        $resultMakeDiet[$dayNumber][$mainNutrition->name] = [
            'totalAmountPerUnit' => $totalAmountPerUnit,
            'meals' => $mealsDataInserted,
        ];


        return ['basicFoods' => $basicFoods, 'mealCount' => count($meals), 'resultMakeDiet' => $resultMakeDiet];
        // get list protein
    }

    public function roundArrayNumbers($array)
    {
        array_walk_recursive($array, function (&$value) {
            if (is_numeric($value)) {
                $value = number_format((float)$value, 2, '.', ''); // Round and format as a string
            }
        });

        return $array;
    }

    public function makeDietJsonFile(DietRequest $diet_request)
    {

        //sort detail by day
        $requestDietDetailCollection = $diet_request->dietRequestDetails()->get()->groupBy(function ($item) {
            return $item->day_number;
        });

        $return = [];


        foreach ($requestDietDetailCollection as $dayNumber => $value) {

            //sort , sorted detail by meal
            $mealIdGroup = $value->groupBy('meal_id');
            $returnTmp = [];
            $returnTmp['can_set_training_day'] = null;
            foreach ($mealIdGroup as $meal_item => $meal_value) {
                $metaValueResult = $meal_value->whereNull('replaced_parent_id')->first();

                // check meal is athlete
                $canSetTrainingDay = false;

                $returnTmp['can_set_training_day'] = null;
                if (isset($diet_request->dietPlan->detail[DietPlan::IS_ATHLETE_MEAL]) && count($diet_request->dietPlan->detail[DietPlan::IS_ATHLETE_MEAL])){
                    $athleteMEal = $diet_request->dietPlan->detail[DietPlan::IS_ATHLETE_MEAL];
                    $canSetTrainingDay = in_array(true, $athleteMEal, true);
                    if ($canSetTrainingDay) {

                        $result = isset($athleteMEal[$meal_item]) && $athleteMEal[$meal_item] === true;
                        $listTrainingDays = isset($diet_request->detail[DietRequest::KEY_DETAIL_TRAINING_DAYS]) ? $diet_request->detail[DietRequest::KEY_DETAIL_TRAINING_DAYS] : [];
                        $isSubmited = in_array($metaValueResult->date_of_day->toDateString(), $listTrainingDays, true);
                        $returnTmp['can_set_training_day'] = [
                            'message' => in_array($metaValueResult->date_of_day->toDateString(),$listTrainingDays) ? 'امروز ورزش کردم' : 'امروز میخواهم ورزش کنم',
                            'is_submitted' => $isSubmited,
                            'can_set_action' => !$metaValueResult->date_of_day->isFuture(),
                        ];

                        if ($result) {
                            if (!in_array($metaValueResult->date_of_day->toDateString(), $listTrainingDays)){
                                continue;
                            }
                        }
                    }
                }

                //sort , sorted detail by main nutrition
                $mainNut = $meal_value->groupBy('main_nutrition');
                $mainNutRet = [];
                $colories = 0;

                $recipe = null;

                foreach ($mainNut as $main_nut_item => $main_nut_value) {
                    $main_nut_value = $main_nut_value->filter(function ($item) {
                        return $item->replaced_parent_id === null;
                    });
                    //final foreach that done by resource , pass the sorted collection to resourese

                    $dietRequestResource = RequestDietDetailResourcev2::make($main_nut_value->first())->resolve()['meals'];
                    $mainNutRet = $dietRequestResource;
//                    if ($main_nut_value->first()->foodable_type == Food::class) {
//                        $basicFoodPivots = $main_nut_value->first()->foodClass->basicFoodPivot;
//                        $numberOfUnit = $main_nut_value->first()->number_of_unit;
//                        foreach ($basicFoodPivots as $basicFoodPivot) {
//                            $dietRequestResource = RequestDietDetailResource::make($basicFoodPivot, null, ['quantity' => ($basicFoodPivot->pivot->quantity * $numberOfUnit),])->resolve();
//                            $mainNutRet[] = array_merge(
//                                ['type' => $main_nut_item],
//                                $dietRequestResource
//                            );
//                        }
//
//                        $arrayList = $main_nut_value->first()->foodClass->basicFoodPivot()->pluck('recipe')->toArray();
//                        $recipe = implode(" ", $arrayList);
//                    }


//                    $colories += $main_nut_value->pluck('calories')->sum();
                    $colories = $main_nut_value->first()->calories;

                }

                //assign date of the request diet detail date to the hand_writtenobject


                $returnTmp['date_of_day'] = verta($metaValueResult->date_of_day)->format('Y/m/d');
                $returnTmp['date_of_day_persian'] = verta($metaValueResult->date_of_day)->formatWord('l') . ' ' . verta($metaValueResult->date_of_day)->format('d/m');
                $returnTmp['colories'] = $colories;
                $returnTmp['recipe'] = $recipe;
                $handsWritten = $this->hasHandwritten($metaValueResult);
                //assign isDone to the meal objcet
                $isDone = $metaValueResult->is_done == true;



                $recipe = $recipe !== null
                    ? ltrim(trim((string) $recipe))
                    : null;
                //find meal name and assign to the index of the object
                $mealName = Meal::firstWhere('id', $meal_item);


                $recipe_id = null;
                if ($metaValueResult->foodable && method_exists($metaValueResult->foodable, 'recipes')) {
                    $recipe_id = optional($metaValueResult->foodable->recipes()->first())->id;
                }

                $consumedFoods = FoodConsumption::where('user_id', $metaValueResult->dietRequest->user_id)
                    ->where('meal_id', $metaValueResult->meal_id)
                    ->whereDate('consumed_at', $metaValueResult->date_of_day)
                    ->get()
                    ->map(function ($food) {
                        return [
                            'type' => 'all',
                            'id' => $food->id,
                            'title' => $food->quantity . ' ' . ($food->unit->name ?? '') . ' ' . ($food->consumable->name ?? ''),
                            'carb' => $food->carb,
                            'protein' => $food->protein,
                            'fat' => $food->fat,
                            'fiber' => $food->fiber,
                            'calorie' => $food->calories,
                        ];
                    });

                $handleMealName = $this->handleMealName($diet_request, $mealName);
                $returnTmp['foods'][] = [
                    'title' => $handleMealName,
                    'icon_url' => $mealName->icon_url ?? url('assets/admin/images/food_icon.png'),
                    'is_done' => $isDone,
                    'cheat_meal' => $metaValueResult->cheat_meal,
                    'priority' => $mealName->priority ?? 1,
                    'hand_written' => $handsWritten,
                    'meal_id' => $metaValueResult->meal_id,
                    'diet_request_id' => $metaValueResult->id,
                    'recipe_id' => $recipe_id,
                    'meals' => $mainNutRet,
                    'consumed_foods' => $consumedFoods,
                    'meal_recipe' => ($recipe || $recipe != "") ? $recipe : null,
                    'meal_calories' => $metaValueResult->calories
                ];

            }
            usort($returnTmp['foods'], function ($a, $b) {
                return $a['priority'] <=> $b['priority'];
            });
            $return[] = $returnTmp;
        }
        return $return;
    }

    public function handleMealName(DietRequest $diet_request, $meal): ?string
    {

//        if(isset($diet_request->dietPlan->detail['fasting']) && isset($diet_request->dietPlan->detail['fasting']['status']) && $diet_request->dietPlan->detail['fasting']['status']){
//            if ($meal->id == 3){
//                return 'وعده قبل قست';
//            }
//            if ($meal->id == 4){
//                return 'میان وعده فست';
//            }
//            if ($meal->id == 5){
//                return 'وعده بعد از فست';
//            }
//        }
        return $meal?->name;
    }

    public function getCheatMealTotal(DietRequest $dietRequest)
    {
        return 1;
    }

    public function canCheat(DietRequest $dietRequest): bool
    {
        return $dietRequest->dietRequestDetails()->cheatMeals()->count() < app('dietService')->getCheatMealTotal($dietRequest);
    }
    public function getComplexFood(DietRequestDetail $detail, $showMaxAllowed = false)
    {
        $resource = RequestDietDetailResourcev2::make($detail)->resolve()['meals'];
        return collect($resource)->map(function ($item) {
            return $item['title'];
        })->toArray();
//        $basicFoodCollection = $detail->foodClass->basicFoodPivot;
//        $returnValue = [];
//        if ($basicFoodCollection->count() > 0) {
//            foreach ($basicFoodCollection as $key => $basicFood) {
//                $maxAllowed = '';
//                if ($showMaxAllowed && $basicFood->pivot->maximum) {
//                    $maxAllowed = ' <span class="tag tag-info">حداکثر ' . $basicFood->pivot->maximum . ' </span>';
//                    if (($basicFood->pivot->quantity * $detail->number_of_unit) > $basicFood->pivot->maximum) {
//                        $maxAllowed .= ' <span class="tag tag-danger">بیشتر از مجاز </span>';
//                    }
//                }
//                $unitName = $basicFood->units()->where('is_primary', '1')->first()?->name ?? $basicFood->units()->first()->name;
//                $returnValue[$key] = ($basicFood->pivot->quantity * $detail->number_of_unit) . ' ' . ($unitName) . ' ' . $basicFood->name . $maxAllowed;
//            }
//        }

//        return $returnValue;
    }

    private function hasHandwritten($dietRequestdetail)
    {
        return null;
        $handswritten = $dietRequestdetail->dietRequest->detail['handwritten'];
        if (isset($handswritten) && array_key_exists($dietRequestdetail->meal_id, $handswritten)) {
            return $handswritten[$dietRequestdetail->meal_id];
        }
        return null;
    }

    private function getMealCalories($mainNutRet): int
    {
        $totalCalories = 0;

        if (count($mainNutRet)) {
            foreach ($mainNutRet as $meal) {
                $totalCalories += floatval($meal['calorie']);
            }
        }

        return (int)$totalCalories;
    }

    public function createDietJsonFile(DietRequest $dietRequest, $data)
    {
        $year = Carbon::parse($dietRequest->created_at)->year;
        $month = Carbon::parse($dietRequest->created_at)->month;

        // Create the directory if it doesn't exist
        $directoryPath = storage_path('app/' . $year . '/' . $month . '/');
        File::makeDirectory($directoryPath, 0755, true, true);

        $jsonString = json_encode($data, JSON_UNESCAPED_UNICODE);

        // File path
        $filePath = $directoryPath . 'json_' . $dietRequest->id . '.json';

        // Now attempt to write the JSON file
        File::put($filePath, $jsonString);

    }

    private function calculatePercentage($referenceNumber, $secondNumber): int
    {
        $percentage = ($secondNumber / $referenceNumber) * 100;
        return (int)round($percentage);
    }

}
