<?php

namespace Modules\Api\Http\Controllers\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\app\Http\Requests\Api\Requests\Profile\DiseasesUserRequest;
use Modules\Api\app\Http\Requests\Api\Requests\Profile\UpdateUserDietInformationRequest;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Api\Enum\RouteEnum;
use Modules\Api\Http\Requests\DietRequest;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\DiseaseResource;
use Modules\Api\Transformers\FaqResource;
use Modules\Core\Entities\Disease;
use Modules\Core\Entities\Faq;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;
use ReflectionClass;
use Verta;

class ProfileController extends Controller
{
    use ApiHandlerTrait;

    const ACTION_TALL = 'tall';
    const ACTION_WEIGHT = 'weight';
    const ACTION_TARGET_WEIGHT = 'target_weight';
    const ACTION_BIRTHDAY = 'birthday';
    const ACTION_FIRST_NAME = 'first_name';
    const ACTION_LAST_NAME = 'last_name';
    const ACTION_CHECKOUT = 'checkout';

    public static function getDefinedConstants(): array
    {
        $reflectionClass = new ReflectionClass(self::class);
        return $reflectionClass->getConstants();
    }

    public function addWeight(Request $request)
    {
        // Validation for the weight input
        $request->validate([
            'weight' => ['required', 'numeric', 'min:30', 'max:300'],
        ], [
            'weight.required' => 'وارد کردن وزن الزامی است.',
            'weight.numeric' => 'وزن باید به صورت عدد وارد شود.',
            'weight.min' => 'وزن نمی‌تواند کمتر از ۳۰ کیلوگرم باشد.',
            'weight.max' => 'وزن نمی‌تواند بیشتر از ۳۰۰ کیلوگرم باشد.',
        ]);

        $user = auth()->user();
        $user->target_weight = request()->input('weight');

        return $this->ok([
            'status' => true,
            'message' => 'وزن با موفقیت ثبت شد'
        ]);
    }
    public function targetPage()
    {
        $user = auth()->user();
        if (!$user->activeDiet()) {
            if ($user->pendingDiet()){
                $banner = [
                    'button' => ButtonResource::make([
                        'title' => 'شما یک رژیم در حال تجویز دارید',
                        'subtitle' => 'بازگشت به پروفایل',
                        'route' => RouteEnum::DASHBOARD,
                        'type' => 2
                    ]),
                    'message' => 'پس از تجویز رژیم از طریق پیامک به شما اطلاع می دهیمser '
                ];
            } else {
                $banner = [
                    'button' => ButtonResource::make([
                        'title' => 'دریافت رژیم اختصاصی',
                        'subtitle' => 'رژیم مناسب شرایط خودتان را بگیرید!!',
                        'route' => RouteEnum::ORDER_PACKAGE,
                        'type' => 2
                    ]),
                    'message' => 'شما هنوز رژیم دریافت نکرده اید . '
                ];
            }

            $target = null;
        } else {
            $banner = null;
            $target = $this->getTargetPage($user);
        }
        return $this->ok([
            'banner' => $banner,
            'target' => $target
        ]);

    }

    private function calculatePercentage($total, $part)
    {
        if ($total == 0) {
            return 0; // جلوگیری از تقسیم بر صفر
        }
        return ($part / $total) * 100;
    }

    private function getTargetDate(User $user , $activeDiet)
    {
        $weightChangePerWeek = (int) $user->weightChangePerWeek;
        if ($activeDiet) {
            $userWeight = $activeDiet->user_information['WEIGHT'] ?? $user->weight;
        } else {
            $userWeight = $user->weight;
        }
        $diffWeight = abs($userWeight - $user->target_weight);

        if ($weightChangePerWeek == 0){
            $diffWeeks = $diffWeight * 1.5;
        } elseif ($weightChangePerWeek == 2){
            $diffWeeks = $diffWeight * 0.5;
        } else{
            $diffWeeks = $diffWeight;
        }
        return Carbon::parse($activeDiet->start_date)->addWeeks(round($diffWeeks));
    }
    public function getTargetWeightAfter(User $user, Carbon|string $date): ?string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $user->metas()
            ->where('meta_key', UserMetaEnum::TARGET_WEIGHT)
            ->where('created_at', '>=', $date)
            ->latest('created_at')
            ->first()?->meta_value;
    }
    private function getTargetPage(User $user)
    {
        $activeDiet = $user->activeDiet();

        $startDate = Carbon::parse($activeDiet->start_date);
        $diffInDays = $startDate->diffInDays(Carbon::now());

        $total = Carbon::parse($activeDiet->end_date)->diffInDays($activeDiet->start_date) ?? 0;
//        $currentDay = Carbon::parse($activeDiet->end_date)->isFuture() ? $diffInDays + 1  : 1;
        $currentDay = Carbon::parse($activeDiet->start_date)->isToday() ? 1 : $diffInDays + 1;

        $userCurrentWeight = app('dietService')->getCurrentWeight($user);
        $userTargetWeight = $activeDiet->user_information['TARGET_WEIGHT'];
        $userTargetWeightAfterGetDiet =  $this->getTargetWeightAfter($user , $activeDiet->start_date);
        if (isset($userTargetWeightAfterGetDiet)){
            $userTargetWeight = $userTargetWeightAfterGetDiet;
        }


        return [
            'id' => $user->id,
            'diet_id' => $activeDiet->id,
            'plane_name' => $activeDiet?->dietPlan->name ?? '-',
            'top_statistics' => [
                'chart' => [
                    'total_day' => max(
                        0,
                        Carbon::parse($activeDiet->end_date)
                            ->diffInDays($activeDiet->start_date)
                    ),

                    'current_day' => max(0, $currentDay),

                    'percent' => max(
                        0,
                        round($this->calculatePercentage($total, $currentDay))
                    ),
                ],
                'tile' => [
//                    'end_at' => getPersianDateSimple($activeDiet->end_date),
                    'end_at' => getPersianDateSimple($this->getTargetDate($user , $activeDiet)),
                    'start_at' => getPersianDateSimple($activeDiet->start_at),
                    'calorie' => $activeDiet->calories,

                    'target_weight' => $userTargetWeight ?? 0,
                    'current_weight' => $userCurrentWeight ?? 0,
                    'start_weight' => app('dietService')->getStartWeight($user) ?? 0,
                ]
            ],
            'add_weight_button' => RouteEnum::ADD_WEIGHT,
            'calculate_target_plan' => [
                 $this->calculateWeightLossPlan($userCurrentWeight , $userTargetWeight , 0.5 , $startDate , '۵۰۰ گرم'),
                 $this->calculateWeightLossPlan($userCurrentWeight , $userTargetWeight , 1 , $startDate ,  '۱ کیلو' ,true),
                 $this->calculateWeightLossPlan($userCurrentWeight , $userTargetWeight , 1.5 , $startDate , '۱.۵ کیلو'),
            ],
            'calorie_distribution_per_week' => [
                'title' => 'تقسیم کالری در هفته',
                'desc' => 'کالری محاسبه شده روزانه',
                'chart' => $this->generateWeeklyCalorieSummary($activeDiet->detail['report_diet']['result'] , $startDate  , Carbon::today())
            ],
        ];

    }

    function generateWeeklyCalorieSummary(array $result, Carbon $startDate, Carbon $currentDate): array
    {
        $weeklySummary = []; // Initialize the array for the weekly summary.

        // Calculate the number of days since the start of the diet.
        $daysPassed = $startDate->diffInDays($currentDate);

        // Determine the current week based on the passed days (weeks start on day 1).
        $currentWeekStartDay = (int)(floor($daysPassed / 7) * 7) + 1;

        // Iterate over the 7 days of the current week.
        for ($i = 0; $i < 7; $i++) {
            $dayNumber = $currentWeekStartDay + $i; // Calculate the day number in the result array.

            if (!isset($result[$dayNumber])) {
                // Skip if the day is out of range.
                continue;
            }

            $dayDate = $startDate->copy()->addDays($dayNumber - 1); // Calculate the actual date for the day.

            $status = $dayDate->lt($currentDate) ? true : false; // Determine if the day is past or upcoming.

            // Calculate the total calories for the day by summing up 'final_calories' in meals.
            $dailyCalories = array_reduce($result[$dayNumber], function ($sum, $meal) {
                return $sum + ($meal['final_calories'] ?? 0); // Add the calories if available.
            }, 0);

            $vDayDate = Verta::instance($dayDate);

            $weeklySummary[] = [
                'day_number' => $dayNumber, // Day number in the diet.
                'day_name' => $vDayDate->formatWord('l'), // Day name (e.g., Monday).
                'date' => getPersianDateSimple($dayDate->toDateString()), // Date in 'Y-m-d' format.
                'status' => $status, // 'past' if day is in the past, otherwise 'upcoming'.
                'total_calories' => $dailyCalories, // Total calories for the day.
            ];
        }

        return $this->updateFirstStatus($weeklySummary);
    }
    function updateFirstStatus(array $days): array
    {
        if (collect($days)->every(fn($day) => $day['status'] === false)) {
            $days[0]['status'] = true;
        }
        return $days;
    }


    public function calculateWeightLossPlan(float $currentWeight, float $targetWeight, float $weeklyWeightLoss , $startDate ,$title = '-', $default = false): array
    {
        // Validate weekly weight loss input
        if ($weeklyWeightLoss <= 0) {
            return [
                'status' => 'error',
                'message' => 'Weekly weight loss must be greater than 0.',
                'total_days' => null,
                'milestones' => [],
            ];
        }

        // Calculate the total weight to lose
        $totalWeightToLose = $currentWeight - $targetWeight;

        if ($totalWeightToLose <= 0) {
            // User has already reached or is below the target weight
            return [
                'total_days' => 0,

                'milestones' => [
                    ['days' => 0, 'weight' => $currentWeight], // Starting point
                ],
            ];
        }

        // Calculate total weeks and total days needed
        $totalWeeks = ceil($totalWeightToLose / $weeklyWeightLoss);
        $totalDays = $totalWeeks * 7;

        // Initialize milestones with the starting weight
        $milestones = [
            [
                'days' => 0,

                'date' => getPersianDateSimple($startDate->format('Y-m-d')),
                'title' => 'شروع',
                'weight' => $currentWeight
            ], // Starting point
        ];

        // Calculate milestones (25%, 50%, 75%, 100%) with corresponding weights
        for ($i = 1; $i <= 3; $i++) {
            $progress = $i / 3; // Progress fraction (25%, 50%, 75%, 100%)
            $daysToMilestone = round($totalDays * $progress); // Days to reach the milestone
            $weightAtMilestone = round($currentWeight - ($totalWeightToLose * $progress), 1); // Weight at the milestone

            $milestones[] = [
                'days' => $daysToMilestone,
                'title' => 'هدف ' . $i,
                'date' => getPersianDateSimple($startDate->copy()->addDays($daysToMilestone)->format('Y-m-d')),
                'weight' => $weightAtMilestone,
            ];
        }

        // Return the results
        return [
            'title' => $title,
            'total_days' => $totalDays,
            'status' => $default,
            'milestones' => $milestones, // Array of milestone days and weights

        ];
    }
    public function faq()
    {
        $faq = FaqResource::collection(Faq::active()->orderBy('id')->get());
        return $this->ok([
            'faq' => $faq,
            'button_icon' => [
                'title' => 'پشتیبانی',
                'popup' => [
                    'title' => setting(SettingKeyEnum::PROFILE_FAQ_TITLE),
                    'phone' => setting(SettingKeyEnum::PROFILE_FAQ_PHONE),
                    'email' => setting(SettingKeyEnum::PROFILE_FAQ_EMAIL),
                    'button' => ButtonResource::make(
                        [
                            'title' => 'تماس با پشتیبانی',
                            'route' => 'tel:' . setting(SettingKeyEnum::PROFILE_FAQ_PHONE)
                        ]
                    )
                ]
            ]
        ]);
    }

    public function diseasesUpdate(DiseasesUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $user->diseases()->sync($request->input('diseases'));
        return $this->ok([
            'status' => true,
            'message' => 'اطلاعا با موفقیت ذخیره شد'
        ]);
    }

    public function diseases()
    {
        $diseases = DiseaseResource::collection(Disease::priority()->get());
        $userDiseases = auth()->user()->diseases;
        return $this->ok([
            'diseases' => $diseases,
            'user_diseases' => DiseaseResource::collection($userDiseases)
        ]);
    }

    public function userInformationUpdate(UpdateUserDietInformationRequest $request)
    {
        $user = auth()->user();
        $action = $request->input('action');
        $value = $request->input('value');
        switch ($action) {
            case self::ACTION_TALL :
                $user->tall = $value;
                $message = 'قد  با موفقیت ویرایش شد';
                $status = true;
                break;
            case self::ACTION_WEIGHT:
                $user->weight = $value;
                $message = 'وزن  با موفقیت ویرایش شد';
                $status = true;
                break;
            case self::ACTION_TARGET_WEIGHT:
                $user->target_weight = $value;
                $message = 'وزن هدف  با موفقیت ویرایش شد';
                $status = true;
                break;
            case self::ACTION_FIRST_NAME:
                $user->first_name = $value;
                $message = 'نام  با موفقیت ویرایش شد';
                $status = true;
                break;
            case self::ACTION_CHECKOUT:
                $user->request_Payment_inviting_friends = 'ثبت درخواست تسویه پرداخت در تاریخ ' . verta()->format('Y/m/d ساعت H:i:s');
                $message = 'ثبت درخواست پرداخت در تاریخ ' . verta()->format('Y/m/d ساعت H:i:s');
                $status = true;
                break;
            case self::ACTION_LAST_NAME:
                $user->last_name = $value;
                $message = 'نام خانوادگی  با موفقیت ویرایش شد';
                $status = true;
                break;
            case self::ACTION_BIRTHDAY:
                $carbonDate = explode("/", $value);

                $jsonData = [
                    'year' => (int)$carbonDate[0],   // Convert to integer for JSON format
                    'month' => (int)$carbonDate[1], // Convert to integer for JSON format
                    'day' => (int)$carbonDate[2],     // Convert to integer for JSON format
                ];
                $birthday = json_encode($jsonData);
                $user->birthday = $birthday;
                $message = 'تاریخ تولد  با موفقیت ویرایش شد';
                $status = true;
                break;
            default:
                $message = 'اشتباه در ارسال پارامتر';
                $status = false;
        }
        if ($status) {
            return $this->ok([
                'status' => true,
                'message' => $message
            ]);
        }
    }


    private function expireDatePackage(User $user)
    {
        if ($user->activePackage()){
            return [
                'title' => 'وضعیت اشتراک',
                'badge_color' => '#6570FF',
                'status_payment' =>(int) now()->diffInDays($user->activePackage()->end_at) . ' روز باقیمانده',
                'button' =>null,
            ];
        } else {
            return [
                'title' => 'وضعیت اشتراک',
                'badge_color' => '#F15928',
                'status_payment' => 'شما دوره فعالی ندارید',
                'button' =>  ButtonResource::make([
                    'title' => 'خرید',
                    'route' =>  RouteEnum::ORDER_PACKAGE,
                    'type' => 1 ,
                ]),
            ];
        }
    }

    public function userInformation()
    {
        $user = auth()->user();
        $inviteFriends = $user->inviteFriends;
//        $user->request_Payment_inviting_friends = null;
        $userMobile = $userEmail = null;
        if (isset($user->email) && $user->email != '') {
            $userEmail = [
                'disable' => true,
                'action' => null,
                'title' => 'آدرس ایمیل',
                'value' => $user->email ?? null,
            ];
        }
        if (isset($user->mobile) && $user->mobile != '') {
            $userMobile = [
                'disable' => true,
                'action' => null,
                'title' => 'شماره موبایل',
                'value' => $user->mobile ?? null,
            ];
        }
        $requestPaymentInviting = $user->request_Payment_inviting_friends;
        return $this->ok([
            'title' => 'اطلاعات کاربری',
            'user_id' => $user->getAuthIdentifier(),
            'items' => [
                'first_name' => [
                    'disable' => false,
                    'title' => 'نام',
                    'action' => self::ACTION_FIRST_NAME,
                    'value' => $user->first_name,
                ],
                'last_name' => [
                    'disable' => false,
                    'action' => self::ACTION_LAST_NAME,
                    'title' => 'نام خانوادگی',
                    'value' => $user->last_name,
                ],
                'subscription' => $this->expireDatePackage($user),
                'mobile' => $userMobile,
                'email' => $userEmail,
                'logout' => [
                    'title' => 'خروج',
                    'route' => RouteEnum::LOGOUT,
                ],
                'invite_friends' => [
                    'number_invited_friends' => $inviteFriends->count() . ' نفر',
                    'benefit' => number_format($inviteFriends->sum('benefit')) . ' ریال',
                    'title' => 'دعوت از دوستان',
                    'first_description' => 'دوستات رو دوعت کن تا هم اونا از یک برنامه عالی استفاده کنن و هم تو درآمد کسب کنی',
                    'invite_code' => $user->invitation_code,
                    'second_description' => 'هر چی زودتر شروع کنی، زودتر درآمدت به حسابت میاد',
                    'show_request_payment' => [
                        'status' => !isset($requestPaymentInviting),
                        'message' => $requestPaymentInviting
                    ],
                    'popup' =>
                        '
همکاری در فروش با “الان وقتشه”

💰 کسب درآمد با معرفی دوستان

هر کاربر می‌تونه یک کد اختصاصی داشته باشه و با دعوت از دیگران، به راحتی درآمد کسب کنه

📌 نحوه محاسبه:
✅ ۵٪ به ازای ۵ نفر اول
✅ ۱۰٪ به ازای ۱۰ نفر اول
✅ ۱۵٪ به ازای ۲۰ نفر اول و بیشتر

💳 نحوه برداشت:
🔸 تا زمانی که تعداد دعوتی‌های شما کمتر از ۲۰ نفر باشه، پول فقط در کیف پول اپلیکیشن ذخیره میشه و می‌تونید ازش برای خرید اشتراک یا خدمات استفاده کنید.
🔸 پس از رسیدن به ۲۰ دعوت موفق، قابلیت برداشت نقدی براتون فعال میشه

🚀 الان وقتشه که درآمد کسب کنی!
کد اختصاصیت رو دریافت کن و دوستات رو دعوت کن تا هم اونا از یک برنامه عالی استفاده کنن و هم تو کسب درآمد کنی!
',

                    'request_payment' => [
                        'action' => self::ACTION_CHECKOUT,
                        'value' => 'checkout',
                        'title' => 'درخواست پرداخت',
                    ]
                ]

            ]

        ]);
    }

    public function dietInformation(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $birthDay = json_decode($user->birth_day);
        return $this->ok([
            'data' => [
                'title' => 'اطلاعات شخصی شما',
                'items' => [
                    'tall' => [
                        'icon' => 'action',
                        'title' => 'قد',
                        'action' => self::ACTION_TALL,
                        'value' => $user->tall . ' سانتی متر',
                    ],
                    'weight' => [
                        'icon' => 'weight',
                        'action' => 'weight',
                        'title' => 'وزن',
                        'value' => $user->weight . ' کیلوگرم',
                    ],

//                    'activity' => [
//                        'icon' => 'activity',
//                        'action' => 'activity',
//                        'title' => 'فعالیت',
//                        'value' => UserMetaEnum::tryFrom(UserMetaEnum::ACTIVITY_PER_WEEK->value)->getOptionName( $user->activityPerWeek) ,
//                    ],
                    'birthday' => [
                        'icon' => 'birthday',
                        'title' => 'تاریخ تولد',
                        'action' => 'birthday',
                        'value' => $birthDay->year . '/' . $birthDay->month . '/' . $birthDay->day,
                        'birthday_array' => $birthDay
                    ],
                    'target_weight' => [
                        'icon' => 'target_weight',
                        'action' => 'target_weight',
                        'title' => 'وزن هدف',
                        'value' => $user->target_weight . ' کیلوگرم',
                    ],
                    'diseases' => [
                        'icon' => 'diseases',
                        'action' => 'diseases',
                        'title' => 'بیماری ها',
                        'value' => $user->diseases->count() . ' مورد',
                    ],
                ]
            ]
        ]);
    }
}
