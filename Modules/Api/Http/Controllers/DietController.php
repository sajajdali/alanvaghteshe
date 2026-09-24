<?php

namespace Modules\Api\Http\Controllers;

use App\Events\RefreshDashboardEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\SetTrainingDayRequest;
use Cache;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Api\app\Http\Requests\RequestNewDietRequest;
use Modules\Api\app\Resources\diet_request_detail\RequestDietDetailResource;
use Modules\Api\app\Resources\ReplaceFoodListResource;
use Modules\Api\app\Resources\RequestDietCollection;
use Modules\Api\app\Resources\RequestDietCompexFoodResource;
use Modules\Api\Http\Controllers\Profile\Consumption\ConsumptionController;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Diet\app\Events\UserFoodConsumptionUpdated;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Diet\Enum\FoodTypeEnum;
use Modules\Diet\Service\DietService;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\Entities\User;
use Verta;

class DietController extends Controller
{
    use ApiHandlerTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $userDietRequest = $user->dietRequests()->where('status', DietRequestStatusEnum::ACTIVE)->orderByDesc('id')->paginate(10);

        return $this->ok(new RequestDietCollection($userDietRequest));
    }

//    public function detail(DietRequest $diet_request)
//    {
//        $user = auth()->user();
//        //if user has not own this diet , return 404
//        if ($diet_request->user_id != $user->id) {
//            return $this->notFound();
//        }
//        $year = Carbon::parse($diet_request->created_at)->year;
//        $month = Carbon::parse($diet_request->created_at)->month;
//
//        $filePath = storage_path('app/' . $year . '/' . $month . '/json_' . $diet_request->id . '.json');
//        $diet_pdf_address = isset($diet_request->detail['pdf_file_address']) ? $diet_request->detail['pdf_file_address'] : null;
//        if (File::exists($filePath) && env('DISABLE_MAKE_JSON_FILE') !== true) {
//            $jsonString = File::get($filePath);
//            $data = json_decode($jsonString, true);
//            return $this->ok(
//                [
//                    'status' => true,
//                    'date_of_day' => verta()->format('Y/m/d'),
//                    'pdf_diet_address' => $diet_pdf_address,
//                    'plan_name' => $diet_request->dietPlan->name,
//                    'target' => $this->getUSerTarget($diet_request),
//                    'cheat_meal' => [
//                        'total_cheats' => 2,
//                        'completed_cheats' => $diet_request->dietRequestDetails()->cheatMeals()->count(),
//                    ],
//                    'start_at' => null,
//                    'data' =>  $this->updateDaysData($data)
//                ]
//            );
//        }
//
//        $result = app('dietService')->makeDietJsonFile($diet_request);
//        app('dietService')->createDietJsonFile($diet_request, $result);
//        return $this->ok(
//            [
//                'status' => true,
//                'date_of_day' => verta()->format('Y/m/d'),
//                'plan_name' => $diet_request->dietPlan->name,
//                'target' => $this->getUSerTarget($diet_request),
//                'cheat_meal' => [
//                    'total_cheats' => 2,
//                    'completed_cheats' => $diet_request->dietRequestDetails()->cheatMeals()->count(),
//                ],
//                'start_at' => null,
//                'pdf_diet_address' => $diet_pdf_address,
//                'data' => $this->updateDaysData($result)
//            ]
//        );
//    }

//    private function handleDietFasting(DietRequest $dietRequest)
//    {
//        if (
//            isset($dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING])
//            &&
//            isset($dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['status']) &&
//            $dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['status']
//        ) {
//            $startAt = $dietRequest->dietPlan->detail[DietPlan::DETAIL_FASTING]['start_at'];
//            if (isset($dietRequest->detail['fasting']) && isset($dietRequest->detail['fasting']['start_at'])) {
//                $startAt = $dietRequest->detail['fasting']['start_at'];
//            }
//            return [
//                'start_at' => (string) $startAt,
//            ];
//        }
//        return null;
//    }

    public function fastingStartAt(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'diet_request_id' => [
                'required',
                Rule::exists('diet_requests', 'id')->where('user_id', $user->id)
            ],
            'start_at' => [
                'required',
                'integer',
                'between:1,24'
            ]
        ], [
            'diet_request_id.required' => 'شناسه درخواست رژیم نباید خالی باشد.',
            'diet_request_id.exists' => 'درخواست رژیم معتبر نیست یا متعلق به این کاربر نیست.',
            'start_at.required' => 'ساعت شروع نباید خالی باشد.',
            'start_at.integer' => 'ساعت شروع باید یک عدد باشد.',
            'start_at.between' => 'ساعت شروع باید بین ۱ تا ۲۴ باشد.'
        ]);

        $dietRequest = DietRequest::find($request->input('diet_request_id'));
        $detail = $dietRequest->detail;
        $detail['fasting'] = [
            'start_at' => $request->input('start_at'),
        ];
        $dietRequest->update(['detail' => $detail]);
        return $this->ok([
            'status' => true,
            'message' => 'تغییر ساعت با موفقیت انجام شد',
            'data' => []
        ]);

    }
    public function detail(DietRequest $diet_request)
    {
        $user = auth()->user();

        // اگر کاربر صاحب این درخواست رژیم نیست، خطای 404 بازگردانده شود
        if ($diet_request->user_id != $user->id) {
            return $this->notFound();
        }

        // مسیر فایل JSON
        $year = Carbon::parse($diet_request->created_at)->year;
        $month = Carbon::parse($diet_request->created_at)->month;
        $filePath = storage_path("app/{$year}/{$month}/json_{$diet_request->id}.json");

        // آدرس فایل PDF
        $diet_pdf_address = $diet_request->detail['pdf_file_address'] ?? null;

        // داده‌های مشترک خروجی
        $baseResponse = [
            'status' => true,
            'date_of_day' => verta()->format('Y/m/d'),
            'plan_name' => $diet_request->dietPlan->name,
            'target' => $this->getUSerTarget($diet_request),
            'fasting_timer' => app(DietService::class)->handleFastingTimer($user),
//            'fasting' => $this->handleDietFasting($diet_request),
            'fasting' => app(DietService::class)->fastingDietStart($diet_request),
            'description' => isset($diet_request->dietPlan->detail['description']) ? $diet_request->dietPlan->detail['description'] : null,
            'shopping_list' => $this->shoppingListSetting($diet_request),
            'cheat_meal' => [
                'total_cheats' => app('dietService')->getCheatMealTotal($diet_request),
                'completed_cheats' => $diet_request->dietRequestDetails()->cheatMeals()->count(),
                'can_cheat' => app('dietService')->canCheat($diet_request),
                'description' => 'شما در وعده آزاد میتوانید آزادانه هر چی دوست دارید بخورد :-) . فقط زیاده روی نکنید که رژیمتون خراب بشه'
            ],

            'start_at' => null,
            'pdf_diet_address' => $diet_pdf_address,
        ];

        // اگر فایل JSON موجود است و DISABLE_MAKE_JSON_FILE فعال نیست
        if (File::exists($filePath) && env('DISABLE_MAKE_JSON_FILE') !== true) {
            $jsonString = File::get($filePath);
            $data = json_decode($jsonString, true);

            return $this->ok(array_merge($baseResponse, [
                'data' => $this->updateDaysData($data , $diet_request),
            ]));
        }

        // تولید فایل JSON جدید
        $result = app('dietService')->makeDietJsonFile($diet_request);
        app('dietService')->createDietJsonFile($diet_request, $result);

        return $this->ok(array_merge($baseResponse, [
            'data' => $this->updateDaysData($result , $diet_request),
        ]));
    }

    /** @return array{active: bool, icon_url: ?string} */
    private function shoppingListSetting(DietRequest $dietRequest): array
    {
        $configuredIcon = setting(SettingKeyEnum::APP_SHOPPING_LIST_ICON);

        return [
            'active' => filter_var(
                setting(SettingKeyEnum::APP_SHOPPING_LIST_ACTIVE),
                FILTER_VALIDATE_BOOLEAN
            ) && (bool) $dietRequest->active
                && $dietRequest->status === DietRequestStatusEnum::ACTIVE,
            'icon_url' => $configuredIcon
                ? (str_starts_with($configuredIcon, 'http://') || str_starts_with($configuredIcon, 'https://')
                    ? $configuredIcon
                    : asset(ltrim($configuredIcon, '/')))
                : null,
        ];
    }

    private function getUSerTarget(DietRequest $dietRequest)
    {
        if (!isset($dietRequest->user_information['DIET_TYPE'])) {
            return 'کاهش وزن';
        }
        if ($dietRequest->user_information['DIET_TYPE'] == 0) {
            return 'کاهش وزن';
        } elseif ($dietRequest->user_information['DIET_TYPE'] == 1) {
            return 'افزایش وزن';
        } else 'تثبیت وزن';
    }

    private function updateDaysData(array $data , $diet_request = null): array
    {
        foreach ($data as $key => &$value) {
            $dateOfDay = Verta::parse($value['date_of_day']);
            $dateOfDay->setTime(23, 59, 59);
            $isToday = $dateOfDay->isToday();
            $isFuture = $dateOfDay->isFuture();

            $value['gregorian_date'] = $dateOfDay->toCarbon()->toDateString();
            $value['accessibility'] = [
                'is_today' => $isToday,
                'add_food' => $isToday || !$isFuture,
                'active_day' => $isFuture || $isToday,
                'active_consumed_meal' => $isToday || !$isFuture,
            ];
            if (isset($value['can_set_training_day']) && isset($value['can_set_training_day']['can_set_action']) && isset($diet_request)) {
                $dateMorning = Verta::parse($value['date_of_day']);
                $dateMorning->setTime(0, 0, 0);

                $listTrainingDays = isset($diet_request->detail[DietRequest::KEY_DETAIL_TRAINING_DAYS]) ? $diet_request->detail[DietRequest::KEY_DETAIL_TRAINING_DAYS] : [];
                $isSubmited = in_array($dateMorning->toCarbon()->toDateString(), $listTrainingDays, true);
                $value['can_set_training_day'] = [
                    'message' => in_array($dateMorning->toCarbon()->toDateString(),$listTrainingDays) ? 'امروز ورزش کردم' : 'امروز میخواهم ورزش کنم',
                    'is_submitted' => $isSubmited,
                    'can_set_action' => !$dateMorning->toCarbon()->isFuture(),
                ];
            }
        }
        return array_values($data);
    }


    private function hasHandwritten($dietRequestdetail)
    {

        $handswritten = $dietRequestdetail->dietRequest->detail['handwritten'];
        if (isset($handswritten) && array_key_exists($dietRequestdetail->meal_id, $handswritten)) {
            return $handswritten[$dietRequestdetail->meal_id];
        }
        return null;
    }

    public function combinedFoodResepie($detail): string
    {
        //collection of the basic food that create a complex food ;
        $basicFoodCollection = $detail->foodClass->basicFoodPivot;
        $returnValue = '';

        if ($basicFoodCollection->count() > 0) {
            $temparr = [];
            foreach ($basicFoodCollection as $key => $basicFood) {
                if ($basicFood->recipe == '') {
                    continue;
                }
                $temparr[] = $basicFood->recipe;
            }
        }
        if (count($temparr) > 0) {
            return implode(' + ', $temparr);
        }
        return '';
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

    public function consumedMeal(Request $request)
    {

        $user = auth()->user();
        if ($request->has('diet_request_detail_id')) {
            $dietRequestSelected = DietRequestDetail::find($request->input('diet_request_detail_id'));

            if (!$dietRequestSelected || $dietRequestSelected->dietRequest->user->id != $user->id) {
                return $this->badRequest(['message' => 'شما دسترسی لازم برای انجام این کار را ندارد']);
            }
            if (DietRequestDetail::where('id', $request->input('diet_request_detail_id'))->whereDate('date_of_day', '>', Carbon::now()->toDateString())->count()) {
                return $this->badRequest(['message' => 'شما در اینده که نمیتونید غذا بخورید :-)']);

            }

            // when is_done is true
            if ($dietRequestSelected->is_done) {
                return $this->ok([
                    'status' => true,
                    'message' => 'قبل شما این غذا را به عنوان میل شده انتخاب کرده اید',
                    'data' => []
                ]);
            }

            // remove user cache
            event(new UserFoodConsumptionUpdated($user));

            $basicFoods = $dietRequestSelected->foodable()->first()->basicFoodPivot()->get();
            if ($basicFoods->count() > 0) {
                $time = explode(":", Carbon::now()->toTimeString());
                $date = Carbon::parse($dietRequestSelected->date_of_day)->setTime($time[0], $time[1], $time[2])->toDateTimeString();

                // new v2
                if (isValidJson($dietRequestSelected->detail)) {
                    $basicFoods = json_decode($dietRequestSelected->detail, true)['food_snapshot']['basic_foods'];
                } else {
                    $basicFoods = $dietRequestSelected->detail['food_snapshot']['basic_foods'];
                }

                foreach ($basicFoods as $basicFood) {
                    if (!BasicFood::where('id', $basicFood['basic_food_id'])->exists()) {
                        continue;
                    }
                    $foodSelected = BasicFood::find($basicFood['basic_food_id']);

                    $consumedFood = new ConsumptionController();
                    $foodUnitId = $basicFood['unit_id'];
                    $foodUnit = $foodSelected->units()->where('food_units.id',$foodUnitId)->firstOrFail();
                    $quantityPerUnit = $foodUnit->pivot->quantity_per_unit;
                    $consumedFood->insertConsumedFood($user ,$foodSelected,$foodUnitId, $quantityPerUnit, $dietRequestSelected->meal_id , $basicFood['quantity'] , $date);
//                    $unit = $basicFood->units()->where('is_primary', '1')->first()?->id ?? $basicFood->units()->first()->id;
//                    $user->foodConsumptions()->create([
//                        'consumable_id' => $basicFood['basic_food_id'],
//                        'consumable_type' => BasicFood::class,
//                        'calories' => $basicFood['calorie'] ?? 0,
//                        'protein' => $basicFood['protein'] ?? 0,
//                        'carb' => $basicFood['carb'] ?? 0,
//                        'fat' => $basicFood['fat'] ?? 0,
//                        'fiber' => $basicFood['fiber'] ?? 0,
//                        'meal_id' => $dietRequestSelected->meal_id,
//                        'food_unit_id' => $basicFood['unit_id'],
//                        'quantity' => $basicFood['quantity'],
//                        'consumed_at' => $date
//                    ]);
                }
            }
            $diet_request = $dietRequestSelected->dietRequest;
            //if user has not own this diet , return 404

            if ($diet_request->user_id != $user->id) {
                return $this->notFound();
            }
            $updatedRequestDiet = DietRequestDetail::where('diet_request_id', $diet_request->id)
                ->where('meal_id', $dietRequestSelected->meal_id)
                ->where('day_number', $dietRequestSelected->day_number)
                ->get();
            if ($updatedRequestDiet->isNotEmpty()) {
                foreach ($updatedRequestDiet as $detailToBeUpdate) {
                    $detailToBeUpdate->update([
                        'is_done' => 1,
                    ]);
                }
                event(new RefreshDashboardEvent($user));
                return $this->ok([
                    'status' => true,
                    'message' => 'غذا با موفقیت ثبت شد',
                    'data' => []
                ]);
            } else {
                return $this->badRequest([
                    'status' => false,
                    'message' => 'وعده مورد نظر پیدا نشد',
                    'data' => []
                ]);
            }
        } else {
            return $this->badRequest(['message' => 'پارامتر های ارسالی ناقص است']);
        }
    }

    public function changeAbleMealList(DietRequestDetail $diet_request_detail)
    {
        //return the list of the food can be replace
        $replaceFood = app('dietService')->replaceMealListFoodsV2($diet_request_detail);
        if ($replaceFood->count() == 0) {
            return $this->ok([
                'status' => false,
                'message' => 'غذایی برایی جایگزینی یافت نشد',
                'data' => null
            ]);
        }
        $transformedData = ReplaceFoodListResource::collection($replaceFood);

        return $this->ok(
            [
                'status' => true,
                'data' => $transformedData
            ]
        );
    }

    public function getPrescribedFood($user, $meal, $date)
    {
        $dietDetails = DietRequestDetail::where('meal_id', $meal->id)
            ->where('date_of_day', $date->toDateString())
            ->whereHas('dietRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($dietDetails->isNotEmpty()) {
            return $dietDetails->map(function ($detail) {
                $consumable = $detail->foodable; // غذای مربوطه
                $unit = $detail->unit; // واحد غذا
                return [
                    'id' => $detail->id,
                    'name' => $consumable->name ?? 'نامشخص',
                    'quantity' => $detail->quantity,
                    'unit' => $detail->quantity . ' ' . ($unit->name ?? ''),
                    'calories' => $detail->calories,
                    'protein' => $detail->protein,
                    'carb' => $detail->carb,
                    'fat' => $detail->fat,
                    'fiber' => $detail->fiber,
                    'date' => verta($detail->consumed_at)->format('Y/m/d : H:i:s'),
                ];
            });
        }

        return null;
    }

    public function change_mealv2(Request $request)
    {
        $user = auth()->user();
        if (!$request->has('diet_request_detail_id') || !$request->has('replaced_id')) {
            return $this->badRequest(['message' => 'پارامتر های ارسالی ناقص است']);
        }

        $dietRequestDetail = DietRequestDetail::find((int)$request->input('diet_request_detail_id'));
        if ($dietRequestDetail->dietRequest->user_id != $user->id) {
            return $this->notFound();
        }

        $res = app('dietService')->replaceDietRequestDetail($dietRequestDetail, $request->input('replaced_id'));
        if ($res['status']) {
            return $this->ok([
                'message' => $res['message'],
                'new_diet_request_detail_id' => $res['new_diet_request_detail_id'],
                'replaced_food' => $res['meal']
            ]);
        } else {
            return $this->badRequest(['message' => $res['message'] ?? 'not found']);
        }

    }

    public function change_meal(Request $request)
    {
        $user = auth()->user();
        if (!$request->has('diet_request_detail_id') || !$request->has('replaced_id')) {
            return $this->badRequest(['message' => 'پارامتر های ارسالی ناقص است']);
        }
        $dietRequestDetail = DietRequestDetail::find((int)$request->input('diet_request_detail_id'));
        if ($dietRequestDetail->foodable_type == BasicFood::class) {
            $replaced_id = BasicFood::find((int)$request->input('replaced_id'));
        } else {
            $replaced_id = Food::find((int)$request->input('replaced_id'));
        }

        if ($dietRequestDetail->dietRequest->user_id != $user->id) {
            return $this->notFound();
        }


        //replace the selected id with new one
        $replaced = app('dietService')->replaceFood($dietRequestDetail, $replaced_id);
        $dietRequestDetailReplaced = DietRequestDetail::find($replaced['diet_request_detail_inserted_id']);

        $mainNutRet = [];

        if ($dietRequestDetail->foodable_type == Food::class) {
//            $title = RequestDietDetailResource::make($dietRequestDetailReplaced, app('dietService')->getComplexFood($dietRequestDetailReplaced));

            $basicFoodPivots = $dietRequestDetailReplaced->foodClass->basicFoodPivot;
            foreach ($basicFoodPivots as $basicFoodPivot) {
                $dietRequestResource = RequestDietDetailResource::make($basicFoodPivot, null, ['quantity' => $basicFoodPivot->pivot->quantity])->resolve();
                $mainNutRet[] = array_merge(
                    ['type' => 'app'],
                    $dietRequestResource
                );
            }

        } else {
//            $title = RequestDietDetailResource::make($dietRequestDetailReplaced)->resolve()['title'];
            $replacedFoodResource = RequestDietDetailResource::make($dietRequestDetailReplaced);
        }

        $recipe = null;
        if ($dietRequestDetailReplaced->foodable_type == Food::class) {
            $arrayList = $dietRequestDetailReplaced->foodClass->basicFoodPivot()->pluck('recipe')->toArray();
            $recipe = implode(" ", $arrayList);
        } else {
            $getAnotherMealInDay = DietRequestDetail::where('day_number', $dietRequestDetailReplaced->day_number)
                ->where('diet_request_id', $dietRequestDetailReplaced->diet_request_id)
                ->where('meal_id', $dietRequestDetailReplaced->meal_id)
                ->whereNull('replaced_parent_id')
                ->get();
            foreach ($getAnotherMealInDay as $basicFood) {
                if ($basicFood->foodable->recipe) {
                    $recipe .= $basicFood->foodable->recipe . "\n";
                }
            }
        }

        $handsWritten = $this->hasHandwritten($dietRequestDetailReplaced);

        if ($replaced['status']) {
            $recipe = ltrim(trim($recipe));
            return $this->ok([
                'status' => true,
                'message' => 'وعده با موفقیت جایگزین شد',
                'replaced_food' => [

                    'title' => $dietRequestDetailReplaced->meal->name,
                    'icon_url' => $dietRequestDetailReplaced->meal->icon_url ?? url('assets/admin/images/food_icon.png'),
                    'is_done' => false,
                    'priority' => $dietRequestDetailReplaced->meal->priority ?? 1,
                    'hand_written' => $handsWritten,
                    'meal_id' => $replaced['diet_request_detail_inserted_id'],
                    'meals' => $mainNutRet,
                    'meal_recipe' => ($recipe || $recipe != "") ? $recipe : null,
                    'meal_calories' => $this->getMealCalories($mainNutRet)

                ],
            ]);
        }
        return $this->requestException([
            'status' => false,
            'message' => 'خطا در جایگزین کردن وعده',
            'data' => []
        ]);
    }

    public function setTrainingDay(SetTrainingDayRequest $request)
    {
        $dietRequest = DietRequest::find($request->input('diet_request_id'));
        $daySelected = $request->input('date');
        $detail = $dietRequest->detail;

        $userTrainingDays = $detail[DietRequest::KEY_DETAIL_TRAINING_DAYS] ?? [];
        // Toggle training day:
        // - If the selected day already exists, remove it (mark as non-training day)
        // - Otherwise, add it to the list (mark as a training day)

        if (in_array($daySelected, $userTrainingDays, true)) {
            $userTrainingDays = array_values(array_filter($userTrainingDays, fn($day) => $day !== $daySelected));
            $message = 'این روز به عنوان روز غیر ورزشی ثبت شد';
        } else {
            $userTrainingDays[] = $daySelected;
            $message = 'روز ورزش با موفقیت برای کاربر ثبت شد';
        }

        // remove json file
        $year = Carbon::parse($dietRequest->created_at)->year;
        $month = Carbon::parse($dietRequest->created_at)->month;
        $filePath = storage_path("app/{$year}/{$month}/json_{$dietRequest->id}.json");
        if (File::exists($filePath)){
            File::delete($filePath);
        }

        $detail[DietRequest::KEY_DETAIL_TRAINING_DAYS] = $userTrainingDays;
        $dietRequest->update(['detail' => $detail]);

        return $this->ok([
            'status' => true,
            'message' => $message
        ]);

    }
    public function newDiet(RequestNewDietRequest $request)
    {
        $user = auth()->user();
        $userWeight = $request->input('weight');
        $plan_id = $request->input('plan_id');
        $target_plan = $request->input('target_plan') ??  $request->input('target_plan_id');

        $user->currentWeight = $userWeight;

        //Disable active user diets
//        $requestDiet = app('dietService')->insertUserDietPlan($user, DietPlan::find($plan_id), $target_plan);
        app('dietService')->generateDiet($user , DietPlan::find($plan_id) , $target_plan);


        return $this->ok([
            'status' => true,
            'diet_id' => null,
            'message' => 'درخواست تجویز رژیم شما ثبت و سیستم در جال تجویز رژیم میباشد'
        ]);
    }

}
