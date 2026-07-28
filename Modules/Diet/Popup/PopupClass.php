<?php

namespace Modules\Diet\Popup;

use Carbon\Carbon;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Api\Enum\PopupEnum;
use Modules\Api\Enum\RouteEnum;
use Modules\Api\Trait\ApiDietTrait;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\User\Enum\UserMetaEnum;

class PopupClass
{
    use ApiHandlerTrait;
    public static function freeAccount()
    {
        return [
            'icon' => asset('assets/admin/images/gift.png'),
            'title' => 'بسته رایگان دو روزه /n تجربه کن انتخاب کن',

            'body' => 'برای ۲ روز رایگان از امکانات اپلیکیشن "الان وقتشه" استفاده کن و ببین چطوره میتونه بهت کمک کنه.
            /n
            در این بسته رایگان فقط به بانک کالری و دستور پخت ها دسترسی داری
            ',
            'description_box' => 'بعد از این دوره، میتونی نسخه کامل رو تهیه کنی و از رژیم های تخصصی متناسب با هدف و نیازت استفاده کنید',

            'buttons' => [
                ButtonResource::make([
                    'title' => 'بستن',
                    'route' => RouteEnum::CLOSE,
                    'is_web_page' => false,
                    'type' => 'danger',
                    'data' => [
                        'field' => PopupEnum::FREE_ACCOUNT->value,
                        'value' => 1
                    ]
                ]),
                ButtonResource::make([
                    'title' => 'شروع رایگان ۲ روزه',
                    'route' => RouteEnum::AGREE,
                    'is_web_page' => false,
                    'type' => 'success',
                    'data' => [
                        'field' => PopupEnum::FREE_ACCOUNT->value,
                        'value' => 1
                    ]
                ])
            ]
        ];
    }
    public static function rejectedPopup()
    {
        return [
            'icon' => 'warning',
            'title' => 'رژیم شما در حال تجویز میباشد',
            'description_box' => null,
            'body' => 'رژیم شما در حال تجویز میباشد و لطفا تا تجویز و ارسال رژیم منتظر بمانید. پس از تجویز یک پیامک برای شمار ارسال خواهد شد',
            'buttons' => [
                ButtonResource::make([
                    'title' => RouteEnum::CLOSE->getName(),
                    'route' => RouteEnum::CLOSE,
                    'type' => 'success',
                    'data' => [
                        'field' => PopupEnum::REJECTED_DIET->value,
                        'value' => 1
                    ]
                ]),

            ]
        ];
    }

    public static function starttingDeitTomorrow()
    {
        $user = auth()->user();
        $user->metas()->where('meta_key', UserMetaEnum::POPUP)->delete();
        $activeDiet = $user->activeDiet();
        if ($activeDiet && $activeDiet->created_at->diffInHours(Carbon::now()) < 24) {
            return [
                'icon' => 'success',
                'title' => 'رژیم شما از فردا شروع میشود',
                'description_box' => null,
                'body' => 'چون رژیم رو بعد از ساعت ۱۲ دریافت کرده‌اید و بخشی از روز گذشته، برای دقت بیشتر در محاسبات، رژیم شما از فردا (ساعت ۱۲ شب امشب) فعال میشه',
                'buttons' => [
                    ButtonResource::make([
                        'title' => RouteEnum::CLOSE->getName(),
                        'route' => RouteEnum::CLOSE,
                        'type' => 'success',
                        'data' => [
                            'field' => PopupEnum::STARTING_THE_DIET_TOMORROW->value,
                            'value' => 1
                        ]
                    ]),

                ]
            ];
        }

    }
    public static function dietIsOverPopup()
    {
        return [
            'icon' => asset('assets/admin/images/gift.png'),
            'title' => 'رژیم شما تمام شده است',
            'description_box' => '🔹 ساخت رژیم جدید هزینه جداگانه‌ای نداره و می‌تونی دوباره رژیمت رو انتخاب یا تغییر بدی.',
            'body' => 'رژیم‌های «الان وقتشه» به‌صورت ۱۵ روزه طراحی می‌شن چون در این بازه ممکنه:
	•	وزنت تغییر کنه
	•	نیاز کالری بدنت کم یا زیاد بشه
	•	یا بخوای نوع رژیمت رو عوض کنی که یکنواخت نباشه

برای اینکه برنامه‌ت همیشه دقیق، به‌روز و متناسب با بدنت باشه، هر ۱۵ روز رژیم جدید برات ساخته می‌شه.

ادامه بده و رژیم جدیدت رو انتخاب کن.',
            'buttons' => [
                ButtonResource::make([
                    'title' => RouteEnum::CLOSE->getName(),
                    'route' => RouteEnum::CLOSE,
                    'type' => 'success',
                    'data' => [
                        'field' => PopupEnum::REJECTED_DIET->value,
                        'value' => 1
                    ]
                ]),

            ]
        ];
    }

    public static function showInformation()
    {
        return [
            'icon' => 'warning',
            'title' => 'توصیه های رژیم',
            'description_box' => null,
            'body' => 'قبل از دریافت رژیم حتما این فایل را مشاهده کنید',
            'buttons' => [
                ButtonResource::make([
                    'title' => 'مشاهده جزئیات',
                    'route' => 'https://alanvaghteshe.com/bmr',
                    'is_web_page' => true,
                    'type' => 'success',
                    'data' => [
                        'field' => null,
                        'value' => null
                    ]
                ]),
                ButtonResource::make([
                    'title' => 'دیگر نشان نده',
                    'route' => RouteEnum::CLOSE,
                    'type' => 'danger',
                    'data' => [
                        'field' => PopupEnum::SHOW_INFORMATION->value,
                        'value' => 1
                    ]
                ]),
            ]
        ];
    }

    public static function popupStore($user, $field, $value): \Illuminate\Http\JsonResponse
    {
        $popupClassInstance = new self(); // Create an instance of PopupClass

        // Define the conditions in an array for efficient lookup
        $conditions = [
            PopupEnum::FREE_ACCOUNT->value => '1',
            PopupEnum::REJECTED_DIET->value => '1',
            PopupEnum::DIET_IS_OVER->value => '1',
            PopupEnum::SHOW_INFORMATION->value => '1',
            PopupEnum::STARTING_TOMORROW->value => '1',
        ];

        // Check if the field and value match any condition
        if (isset($conditions[$field]) && $conditions[$field] === $value) {
            // Delete user metas with the specified meta key
            $user->metas()->where('meta_key', UserMetaEnum::POPUP)->delete();

            return $popupClassInstance->ok([
                'status' => true,
                'message' => 'اطلاعات ثبت شد'
            ]);
        }

        // Default response when conditions are not met
        return $popupClassInstance->ok([
            'status' => true,
            'message' => 'چیزی یافت نشد'
        ]);
    }

}
