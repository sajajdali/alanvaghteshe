<?php

namespace Modules\Api\Http\Controllers\Profile;

use App\Events\RefreshDashboardEvent;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Modules\Api\app\Http\Requests\PopupRequest;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Api\Emails\RegisterMail;
use Modules\Api\Enum\PopupEnum;
use Modules\Api\Enum\RouteEnum;
use Modules\Api\Trait\ApiDietTrait;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\Notification\NotificationResource;
use Modules\Chat\app\Models\Chat;
use Modules\Course\app\Models\CourseAppBanner;
use Modules\Diet\app\Events\DietRequestAdded;
use Modules\Diet\app\Listeners\InvalidateUserMealsCache;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Diet\Popup\PopupClass;
use Modules\Diet\Service\DietService;
use Modules\Package\Enum\PackageTypeEnum;
use Modules\Recipe\app\Models\Recipe;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\app\Notifications\UserMessageNotification;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;

class DashboardController extends Controller
{
    use ApiHandlerTrait,ApiDietTrait;

    public function seeder()
    {
        $modelName = 'RecipeUnit';

        // دریافت نام جدول از مدل
        $modelClass = "Modules\\Recipe\\app\\Models\\$modelName"; // نام کامل مدل به همراه namespace
        if (!class_exists($modelClass)) {
            dd("Model $modelName does not exist.");
        }

        // دریافت داده‌ها از جدول
        $data = $modelClass::all()->toArray();

        // ساخت مسیر ذخیره
        $directoryPath = storage_path("app/seeders/");
        $fileName = "{$modelName}.json";

        // اطمینان از وجود پوشه
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // ذخیره اطلاعات در فایل
        file_put_contents("$directoryPath/$fileName", json_encode($data, JSON_PRETTY_PRINT));

        dd("Seeder file created at $directoryPath/$fileName");
    }
    public function seederPivot()
    {
        // نام جدول واسط
        $tableName = 'basic_food_category';

        // دریافت داده‌ها از جدول به صورت مستقیم
        $data = DB::table($tableName)->get()->toArray();

        // ساخت مسیر ذخیره
        $directoryPath = storage_path("app/seeders/");
        $fileName = "{$tableName}.json";

        // اطمینان از وجود پوشه
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // ذخیره اطلاعات در فایل
        file_put_contents("$directoryPath/$fileName", json_encode($data, JSON_PRETTY_PRINT));

        dd("Seeder file created at $directoryPath/$fileName");
    }
    public function getInverseOptions(string $options): string
    {
        $optionsArray = json_decode($options, true);
        $fullRange = range(0, 6);
        if (empty($optionsArray)) {
            return json_encode($fullRange);
        }
        $inverseOptions = array_values(array_diff($fullRange, $optionsArray));
        return empty($inverseOptions) ? json_encode([]) : json_encode($inverseOptions);
    }

    public function test()
    {
        $basicFoods = BasicFood::where('name' , 'LIKE' , '%قیمه%')->get();
        $foodsJson  = $basicFoods->toJson(JSON_UNESCAPED_UNICODE);

        $response = Http::withToken(env('OPENAI_API_KEY'))->asJson()
            ->withOptions([
                'http_errors'     => false, // 4xx/5xx هم exception نده
                'connect_timeout' => 0,     // ⬅️ بدون محدودیت اتصال
                'timeout'         => 0,     // ⬅️ بدون محدودیت کل
                'read_timeout'    => 0,     // ⬅️ بدون محدودیت خواندن (Guzzle)
            ])
            ->post('https://api.openai.com/v1/responses', [
                "model" => "gpt-5-nano",
                "input" => [
                    ["role" => "system", "content" => "خیلی خلاصه با توجه به این اطلاات پاسخ بده " . $foodsJson],
                    ["role" => "user", "content" => "اطلاعات غذای قیمه و جزئیاتشو از این لیستی که بهت دادم استخراج میکنی - فقط کالری رو در نظر بگیر وقتی میخوای استخراج کنی که کدوم ردیف کالر ی منطقی تری باسه قیمه داره "],
                ],
                "stream" => false,
            ]);

        return $response->successful() ? $response->json() : [
            'ok' => false,
            'status' => $response->status(),
            'message' => 'پاسخی از سرور دریافت نشد (بدون exception).',
        ];

        // اینجا هیچ Exceptionی پرتاب نمیشه، حتی اگر timeout یا خطا باشه
        if ($response->successful()) {
            return $response->json();
        }

        return [
            'ok' => false,
            'status' => $response->status(),
            'message' => 'پاسخی از سرور دریافت نشد',
        ];
    }
    public function testNotification()
    {
        $user = User::with('userDevices')->find(2523);

        try {
            $user->notify(new UserMessageNotification(
                title: "تیکت شما جواب داده شد",
                excerpt: 'به تیکت ارسالی شما پاسخ دادیم',
                message: 'salam',
            ));
        } catch (\Throwable $e) {
            \Log::error('Error on notify', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function checkEndDietPopup(User $user): bool
    {
        return
            $user->activePackage() !== null
            && $user->dietRequests()->exists()
            && $user->completedDietsQuery()->exists()
            && $user->activeDiet() === null;
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        // hande date
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $currentDay = $date->toDateString();
        $nextDay = $date->copy()->addDay()->toDateString();
        $prevDay = $date->copy()->subDay()->toDateString();

        // Calculation section
        $userBmi = app('dietService')->userBmi($user , true);
        $userFood = app('dietService')->userFoodConsumptionPerWeek($user , $date);
        $userWater = app('dietService')->userWaterConsumptionPerDay($user, $currentDay);
        $userMeals = app('dietService')->getUserMeals($user, $userBmi->calorie, $currentDay);

        $userFoodThisDay = $userFood[$currentDay];
        $popUp = $this->popupHandle($user);

        return $this->ok([

            'user_id' => $user->id,
            'header' => [
                'messages' => $user->messages()->where('is_read', 0)->count(),
                'instagram' => setting(SettingKeyEnum::INSTAGRAM),
            ],
            'dates' => [
                'title' => (Carbon::today()->toDateString() == $currentDay ? 'امروز, ' : '') .verta($date)->format('d F'),
                'today' => Carbon::now()->toDateString(),
                'current_day' => $currentDay,
                'next_day' => $nextDay,
                'prev_day' => $prevDay,

            ],


            'show_diet_banner' => $this->showDietBanner($user),
            'course_banners' => $this->getCourseBanners(),
            'fasting_timer' => app(DietService::class)->handleFastingTimer($user),
            'top_chart' => [
                'calorie_this_day' => [
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

            'user_water' => $userWater,
            'meals' => $userMeals,
//            'has_active_diet' => $this->userHasActiveDiet($user),
//            'diet_order_button' => $this->dietOrderButton($user),
            'diet_order_button' =>null,
            'checklist' => null,

            'weekly_feed_history' => [
                'title' => 'تاریخچه خوراک هفتگی',
                'chart' => app('dietService')->weeklyFeedHistory($user)
            ],
            'invite_friends' => [
                'title' => 'دعوت از دوستان',
                'sub_title' => 'دوستانت رو دعوت کن تا برنامت رایگان بشه',
                'button' => ButtonResource::make([
                    'title' => 'دعوت دوستان',
                    'subtitle' => '',
                    'route' => RouteEnum::PROFILE,
                ])
            ],

            'notifications' => NotificationResource::collection($user->unreadNotifications),

            'blog' => $this->getPosts() ,
            'popup' => $popUp,
            'online_support' => [
                'active' => true,
                'bg_color' => '#0CDA6B',
                'badge' => Chat::getRoom($user->id)->new_message_by_user,
                'chat_id' => Chat::getRoom($user->id)->id
            ]

        ]);
    }

    private function getCourseBanners(): array
    {
        $banners = CourseAppBanner::query()
            ->with('course:id,title,slug')
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('priority')
            ->orderBy('sort_order')
            ->get();

        return [
            'top' => $this->transformCourseBanners($banners->where('position', CourseAppBanner::POSITION_TOP)->values()),
            'middle' => $this->transformCourseBanners($banners->where('position', CourseAppBanner::POSITION_MIDDLE)->values()),
        ];
    }

    private function transformCourseBanners($banners): array
    {
        return $banners->map(function (CourseAppBanner $banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'image' => $banner->image,
                'position' => $banner->position,
                'sort_order' => $banner->sort_order,
                'priority' => $banner->priority,
                'course_id' => $banner->course_id,
                'course_title' => $banner->course?->title,
                'button' => ButtonResource::make([
                    'title' => $banner->title,
                    'subtitle' => $banner->course?->title ?? '',
                    'route' => RouteEnum::COURSE_DETAIL,
                    'data' => [
                        'course_id' => $banner->course_id,
                        'slug' => $banner->course?->slug,
                    ],
                ]),
            ];
        })->all();
    }

    private function getPosts()
    {
        // Cache key based on today's date
        $cacheKey = 'daily_blog_posts_' . now()->toDateString();

        // Check if cache exists, if not, fetch from API and cache it
        return Cache::remember($cacheKey, now()->addDay(), function () {
            // Make an API request to the WordPress site
            $response = Http::get('https://alanvaghteshe.com/wp-json/wp/v2/posts', [
                'per_page' => 5, // Limit the number of posts to 5
            ]);

            // If the request fails, return an error structure
            if ($response->failed()) {
                return [
                    'title' => 'Error',
                    'message' => 'خطا در دریافت اطلاعات از وردپرس',
                    'posts' => [],
                ];
            }

            // Process the data and format it
            $posts = collect($response->json())->map(function ($post) {
                return [
                    'title' => $post['title']['rendered'] ?? 'No title',
                    'link' => $post['link'] ?? '#', // WordPress post URL
                    'image' => null, // Placeholder for an image, if needed
                ];
            });

            // Final data structure for caching and returning
            return [
                'title' => 'بیشتر بدانید',
                'description' => 'در این بخش می‌توانید اطلاعات مهم مرتبط با رژیم غذایی و برنامه غذایی تون رو ببینید',
                'posts' => $posts,

            ];
        });
    }
//    private function getPosts()
//    {
//        $response = Http::get('https://alanvaghteshe.com/wp-json/wp/v2/posts', [
//            'per_page' => 5,
//        ]);
//
//        if ($response->failed()) {
//            return response()->json([
//                'message' => 'خطا در دریافت اطلاعات از وردپرس',
//            ], 500);
//        }
//
//        $posts = collect($response->json())->map(function ($post) {
//            return [
//                'title' => $post['title']['rendered'] ?? 'بدون عنوان',
//                'link' => $post['link'] ?? '#', // لینک اصلی پست وردپرس
//                'image' => null,
//            ];
//        });
//
//        return [
//                'title' => 'بیشتر بدانید',
//                'description' => 'در این بخش می‌توانید اطلاعات مهم مرتبط با رژیم غذایی و برنامه غذایی تون رو ببینید',
//                'posts' => $posts,
//
//        ];
//    }

    private function popupHandle(User $user): ?array
    {
        $popupUser = $user->popup;

        // internal check
        if ($this->checkEndDietPopup($user)){
            return PopupClass::dietIsOverPopup();
        }

        // start diet tomorrow
        if ($user->activeDiet() && $user->activeDiet()?->dietRequestDetails()?->first()?->date_of_day){
            if (Carbon::parse($user->activeDiet()->dietRequestDetails()?->first()->date_of_day)->isTomorrow()){
                return PopupClass::starttingDeitTomorrow();
            }
        }

        // check from user attribute
        if (!$popupUser){
            return null;
        }

        if ($user->popup == PopupEnum::SHOW_INFORMATION->value){
            return PopupClass::showInformation();
        }
        elseif ($user->popup == PopupEnum::FREE_ACCOUNT->value){
            return PopupClass::freeAccount();
        }
        elseif ($user->popup == PopupEnum::REJECTED_DIET->value){
            return PopupClass::rejectedPopup();
        }
//        elseif ($user->popup == PopupEnum::STARTING_THE_DIET_TOMORROW->value){
//            return PopupClass::starttingDeitTomorrow();
//        }
        return null;


    }

    public function popup(PopupRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $field = $request->input('field');
        $value = $request->input('value');

        return PopupClass::popupStore($user, $field, $value);

    }
    private function showDietBanner($user)
    {
        // temp
        //TODO must be delete
//        return [
//            'chips' => '9 روز',
//            'button' => ButtonResource::make([
//                'title' => 'مشاهده برنامه رژیم من',
//                'subtitle' => 'مشاهده غذاهای پیشنهادی شما',
//                'route' =>  RouteEnum::ORDER_PACKAGE
//                'route' =>  RouteEnum::SHOW_DIET->parameter(2)
//            ]),
//        ];
        $userActiveDiet = $this->userHasActiveDiet($user);

        $countdownTimer = null;
        if ($userActiveDiet){

            return [
                'bg_color' => [
                    '#084976',
                    '#04A097',
                    '#007BD3',
                ],
                'chips' => round(Carbon::now()->diffInDays($userActiveDiet->start_date) + 1),
                'button' => ButtonResource::make([
                    'title' => 'مشاهده برنامه رژیم من',
                    'subtitle' => 'مشاهده غذاهای پیشنهادی شما',
                    'route' =>  RouteEnum::SHOW_DIET->parameter($userActiveDiet->id),
                    'type' => 1 ,

                ]),
                'cheat_meal' => [
                    'total_cheats' => app('dietService')->getCheatMealTotal($userActiveDiet),
                    'completed_cheats' => $userActiveDiet->dietRequestDetails()->cheatMeals()->count(),
                    'can_cheat' =>  app('dietService')->canCheat($userActiveDiet),
                    'description' => 'شما در وعده آزاد میتوانید آزادانه هر چی دوست دارید بخورد :-) . فقط زیاده روی نکنید که رژیمتون خراب بشه'
                ],
            ];
        } else{
            if ($user->pendingDiet()){
                return [
                    'bg_color' => [
                        '#084976',
                        '#04A097',
                        '#007BD3',
                    ],
                    'chips' => null,
                    'button' => ButtonResource::make([
                        'title' => 'رژیم شما در حال تجویز است',
                        'subtitle' => 'لطفا تا تجویز رژیم منتظر بمانید . پس از تجویز از طریق پیامک به شما اطلاع میدهیم',
                        'route' => RouteEnum::DASHBOARD,
                        'type' => 2,
                    ]),
                    'cheat_meal' => null,
                ];
            } else {
                return [
                    'bg_color' => [
                        '#084976',
                        '#04A097',
                        '#007BD3',
                    ],
                    'chips' => null,
                    'button' => ButtonResource::make([
                        'title' => 'دریافت رژیم اختصاصی',
                        'subtitle' => 'رژیم مناسب شرایط خودتان را بگیرید!!',
                        'route' => RouteEnum::ORDER_PACKAGE,
                        'type' => 2,
                    ]),
                    'cheat_meal' => null,
                ];
            }

        }
    }

    public function userHasActiveDiet($user)
    {
        return app('dietService')->userActiveDiet($user);
    }

    private function dietOrderButton($user): ?array
    {
        if ($this->userHasActiveDiet($user)) {
            return null;
        }
        return [
            'title' => 'رژیم تخصصیت رو دریافت کن!',
            'button' => ButtonResource::make([
                'title' => 'خرید رژیم',
                'subtitle' => null,
                'route' =>  null
            ]),
            'descriptions' => [
                'توی یک ماه 6 کیلو کم کن',
                'با این رژیم ماهیچه‌هاتو از دست نمیدی',
                'پشتیبانی 24 ساعته داری',
                'و کلی ویژگی‌های دیگر'
            ]
        ];
    }

    private function checklist($user)
    {
        return [
            'title' => 'چک لیست کارای روزانه‌ت',
            'step' => '2-4',
            'percent' => 50,
            'items' => [

                [
                    'button' => ButtonResource::make([
                        'title' =>'🧑🏻' . ' اطلاعات شخصیت را وارد کن.',
                        'subtitle' => null,
                        'route' =>  null
                    ]),
                    'status' => true
                ],
                [
                    'button' => ButtonResource::make([
                        'title' =>'🍞' . ' آخرین غذایی که خوردی رو ثبت کن.',
                        'subtitle' => null,
                        'route' =>  null
                    ]),
                    'status' => false

                ],
                [
                    'button' => ButtonResource::make([
                        'title' =>'🍇' .  ' غذاهای امروزت رو کامل وارد کن.',
                        'subtitle' => null,
                        'route' =>  null
                    ]),
                    'status' => true
                ],
                [
                    'button' => ButtonResource::make([
                        'title' =>'🌟' . ' پروفایلت رو تکمیل کن.',
                        'subtitle' => null,
                        'route' =>  null
                    ]),
                    'status' => true
                ]
            ]
        ];
    }

    public function caculateColories($userActiveDiet): array
    {
        $colories = [
            'previous_day_calories' => 0,
            'total_Today_colories' => 0,
            'total_Today_counsumed_colories' => 0,
            'Used_carb' => 0,
            'Used_carb_persentage' => 0,
            'Used_fat' => 0,
            'Used_fat_persentage' => 0,
            'Used_protein' => 0,
            'Used_protein_persentage' => 0,
        ];

        if (!empty($userActiveDiet)) {
            $colories['previous_day_calories'] = ($userActiveDiet->dietRequestDetails()->whereDate('date_of_day',
                Carbon::now()->subDay())->sum('calories'));
            $colories['total_Today_colories'] = ($userActiveDiet->dietRequestDetails()->whereDate('date_of_day',
                Carbon::now())->sum('calories'));
            $colories['total_Today_counsumed_colories'] = ($userActiveDiet->dietRequestDetails()->where('is_done',
                1)->whereDate('date_of_day', Carbon::now())->sum('calories'));
            $colories['Used_fat'] = $userActiveDiet->dietRequestDetails()->where('is_done',
                1)->whereDate('date_of_day', Carbon::now())->sum('fat') ?? 0;
            $totalFat = $userActiveDiet->dietRequestDetails()->whereDate('date_of_day', Carbon::now())->sum('fat');
            if ($totalFat > 0) {
                $colories['Used_fat_persentage'] = floor(($colories['Used_fat'] / $totalFat) * 100);
            }
            $colories['Used_protein'] = $userActiveDiet->dietRequestDetails()->where('is_done',
                1)->whereDate('date_of_day', Carbon::now())->sum('protein') ?? 0;
            $totalpro = $userActiveDiet->dietRequestDetails()->whereDate('date_of_day', Carbon::now())->sum('protein');
            if ($totalpro > 0) {
                $colories['Used_protein_persentage'] = floor(($colories['Used_protein'] / $totalpro) * 100);
            }
            $colories['Used_carb'] = $userActiveDiet->dietRequestDetails()->where('is_done',
                1)->whereDate('date_of_day', Carbon::now())->sum('carb') ?? 0;
            $totalCarb = $userActiveDiet->dietRequestDetails()->whereDate('date_of_day', Carbon::now())->sum('carb');
            if ($totalCarb > 0) {
                $colories['Used_carb_persentage'] = floor(($colories['Used_carb'] / $totalCarb) * 100);
            }
        }

        return $colories;
    }

    public function weightChart()
    {
        /** @var User $user */
        $user = auth()->user();
        $userActiveDiet = $user->dietRequests()
            ->whereNotNull('diet_plan_id')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();


        $chart_detail = [];
        if (!empty($userActiveDiet)) {
            $i = 0;
            foreach ($userActiveDiet as $userDiets) {
                $chart_detail[$i]['value'] = (int)$userDiets->user_information['WEIGHT'];
                $chart_detail[$i]['label'] = verta($userDiets->start_date)->format('d Y %B ');
                $i++;
                $chart_detail[$i]['value'] = (int)$userDiets->user_information['WEIGHT'] - 10;
                $chart_detail[$i]['label'] = verta($userDiets->start_date)->addDays(3)->format('d Y %B ');
                $i++;
                $chart_detail[$i]['value'] = (int)$userDiets->user_information['WEIGHT'] - 20;
                $chart_detail[$i]['label'] = verta($userDiets->start_date)->addDays(5)->format('d Y %B ');
            }
        }

        $start_at = '';
        $end_at = '';
        if ($userActiveDiet->last() && $userActiveDiet->last()->start_date) {
            $start_at = verta($userActiveDiet->last()->start_date)->format('Y/m/d');
            $end_at = verta($userActiveDiet->last()->end_date)->format('Y/m/d');
        }
        return $this->ok(
            [
                'status' => true,
                'title' => 'وضعیت شما',
                'sub_title' => 'چقد تا هدف مونده',
                'chart_data' => $chart_detail,
                'have_diet' => count($chart_detail) > 0,
                'target_weight' => (int)$user->target_weight,
                'start_weight' => (int)$userActiveDiet->last()?->user_information['WEIGHT'] ?? $user->weight,
                'end_date' => $start_at,
                'start_date' => $end_at,
                'description' => (string)setting(\Modules\Setting\Enum\SettingKeyEnum::WEIGHT_CHART_DESCRIPTION_APP),
            ]
        );
    }

    private function activeNextSession(User $user): bool
    {
        $activePackage = $user->activePackage();
        if ($activePackage) {
            if ($activePackage->package->type == PackageTypeEnum::DIET) {

                return $user->dietRequests()->where('status', DietRequestStatusEnum::ACTIVE)->where('active',
                    1)->whereNotNull('start_date')->whereDate('end_date', '<', Carbon::now())->exists();
            }
            if ($activePackage->package->type == PackageTypeEnum::DIET_AND_EXERCISE) {
                $diet = $user->dietRequests()->where('status', DietRequestStatusEnum::ACTIVE)->where('active',
                    1)->whereNotNull('start_date')->whereDate('end_date', '<', Carbon::now()) ?? false;
                $exercise = $user->exercisePlanRequests()->whereDate('end_at', '<=', Carbon::now())->where('status',
                    ExercisePlanRequestEnum::COMPLETED);
                if ($diet || $exercise) {
                    return true;
                }
                return false;
            }
            if ($activePackage->package->type == PackageTypeEnum::EXERCISE) {
                return $user->exercisePlanRequests()->whereDate('end_at', '<=', Carbon::now())->where('status',
                    ExercisePlanRequestEnum::COMPLETED)->exists();
            }
        }
        return false;
    }

    private function getGreetingBasedOnTime(): string
    {
        // Get the current time using Carbon
        $currentTime = Carbon::now();

        // Get the hour from the current time
        $currentHour = $currentTime->hour;

        // Determine whether it is morning, noon, afternoon, or night
        if ($currentHour >= 5 && $currentHour < 12) {
            // Morning (5:00 AM to 11:59 AM)
            return "صبح بخیر";
        } elseif ($currentHour >= 12 && $currentHour < 17) {
            // Noon (12:00 PM to 4:59 PM)
            return "ظهر بخیر";
        } elseif ($currentHour >= 17 && $currentHour < 20) {
            // Afternoon (5:00 PM to 7:59 PM)
            return "بعد از ظهر بخیر";
        } else {
            // Night (8:00 PM to 4:59 AM)
            return "شب بخیر";
        }
    }
}
