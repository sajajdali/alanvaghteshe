<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Enum\ConditionKeyEnum;

class DietRequestAndOrderController extends Controller
{
    private function getTargetButtons($dietPlan)
    {
        return $dietPlan->conditions()
            ->where('key', ConditionKeyEnum::DIET_TYPE)
            ->first()
            ->options['items']['values'];
    }

    public function getInformationPages()
    {
        $user = auth()->user();
        $result = [];
        $dietPlans = DietPlan::active()->specificPattern();
        $selectTargetButtons = $dietPlans->first()->conditions()->where('key', ConditionKeyEnum::DIET_TYPE)->first()->options['items']['values'];

        foreach ($selectTargetButtons as $key => $button) {
            $thisCondition[ConditionKeyEnum::DIET_TYPE->value] = [$key];
            $dietPlans = DietPlan::active()->specificPattern();
            $dietPlanHaveThisCondition = app('dietService')
                ->handleConditionInModel($dietPlans, $thisCondition)
                ->whereJsonContains('detail->application->status', true)
                ->orderBy('detail->application->priority', 'asc');
            $result[$key] = [
                'target_plan_id' => $key,
                'title' => $button,
                'items' => $this->handleItems($dietPlanHaveThisCondition->get())
            ];
        }

        $getWeightPage = null;
        if ($user->activePackage() && $user->activePackage()->package->id != 4){
            $target_page = 'new_diet';
        } else {
//            if ($user->dietRequests()->count()){
//                $getWeightPage = $this->getWeight($user);
//                $target_page = 'order_package_and_new_diet';
//            } else {
                $target_page = 'order_package';
//            }
        }

        return [
            'user' => $user->id,
            'target_page' => $target_page,
            'select_diet_plan' => $result,
            'get_weight' => $this->getWeight($user),
            'support_page' => $this->supportPage()
        ];
    }

    private function getWeight($user)
    {
        return [
            'header' => 'وزن شما',
            'title' => 'الان چند کیلویی؟',
            'description' => 'اینجا وزن همین الانت رو وارد کند تا بتونیم متناسب با شرایطت، بهترین برنامه رو برات تنظیم کنیم.',
            'current_weight' => (float) app('dietService')->getCurrentWeight($user),
        ];
    }
    private function supportPage()
    {
        return [
            'header' => 'پشتیبانی تخصصی شما',
            'title' => 'تفاوت پشتیبانی اختصاصی و معمولی',
            'description' => [
                'first' => 'الان وقتشه برای همه کاربران اهمیت زیادی قائله و خدمات تغذیه‌ای برای همه یکسانه. تنها تفاوت نوع پشتیبانیه.',
                'second' => 'اگر حس می‌کنی رژیمتو مدام میشکنی، توی رژیم گرفتن گیج میشی، شرایط خاص مثل دیابت، کبدچرب، کلسترول، بارداری و... داری، یا حتی دوست داری کسی مرتب وضعیتت رو چک کنه
                و آنالیز دقیق بهت بده، پلن VIP برای تو طراحی شده!'
            ],
            'active_support' => [
                'title' => ' فعال سازی پشتیبانی تخصصی (VIP)',
                'description' => 'با فعالسازی این مورد، پشتیبانی اختصاصی همیشه کنارتونه'
            ],
            'details' => [

                [
                    'name' => 'برنامه غذایی تخصصی',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'آنالیز روزانه و هفتگی',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'تعیین هدف',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'دسترسی به رژیم‌های متنوع',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'مشاوره و پشتیبانی در اپلیکیشن',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'مشاوره تلفنی نامحدود',
                    'items' => [
                        'without_support' => false,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'پشتیبان اختصاصی',
                    'items' => [
                        'without_support' => false,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'برنامه غذایی برای بیماری‌های خاص',
                    'items' => [
                        'without_support' => false,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'دستور پخت غذاها',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],

                [
                    'name' => 'حالت روزه داری',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'آب شمار',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'غذاساز شخصی',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'ویرایش غذاها',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'مشاهده نمودار و گزارشات',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'بررسی و آنالیز هفتگی پشتیان',
                    'items' => [
                        'without_support' => false,
                        'having_support' => true,
                    ]
                ],
                [
                    'name' => 'ویدئوهای ورزشی',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],[
                    'name' => 'کالری شمار',
                    'items' => [
                        'without_support' => true,
                        'having_support' => true,
                    ]
                ],
            ] ,
            'popup' => [
                'title' => 'دریافت برنامه بدون پشتیبانی اختصصای',
                'body' => [
                    'پشتیبانی اختصاصی بهت کمک میکنه بیشترین بهره رو از اپلیکیشن و پیشرفتت ببری',
                    'اما اگر احساس میکنی فعلا نیازی به این امکانات نداری، میتونی بدون پشتیبانی اختصاصی هم ادامه بدی'
                ],
                'buttons' => [
                    'accept' => 'ادامه با پشتیبان اختصاصی',
                    'without_support' => 'ادامه بدون پشتیبان اختصاصی',
                ]
            ]
        ];
    }

    private function handleItems($dietPlans)
    {
        $resultItems = [];
        if ($dietPlans->count() === 0) {
            return [];
        }
        foreach ($dietPlans as $dietPlan) {
            $application = $dietPlan->detail['application'];
            unset($application['status']);
            $application['plan_id'] = $dietPlan->id;
            $resultItems[] = $application;
        }
        return $resultItems;
    }
}
