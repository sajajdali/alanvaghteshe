<?php

return [
//    'base' => [
//        'title' => 'تنظیمات پایه',
//        'icon' => 'fa fa-gear',
//        'settings' => [
//            \Modules\Setting\Enum\SettingKeyEnum::BASE_TITLE,
//        ],
//    ],
    'exercise' => [
        'title' => 'ورزش‌ها',
        'icon' => 'fa fa-futbol-o',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::DEFAULT_EXERCISE_STATUS
        ],
    ],
    'sms' => [
        'title' => 'پیامک',
        'icon' => 'fa fa-mobile',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::SMS_API_TOKEN,
            \Modules\Setting\Enum\SettingKeyEnum::SMS_API_LOGIN_TEMPLATE,
            \Modules\Setting\Enum\SettingKeyEnum::SMS_AFTER_SEND_DIET,
            \Modules\Setting\Enum\SettingKeyEnum::SMS_AFTER_ORDER_PACKAGE,
            \Modules\Setting\Enum\SettingKeyEnum::SMS_AFTER_ORDER_COURSE,
            \Modules\Setting\Enum\SettingKeyEnum::SMS_SEND_APP_LINK
        ],
    ],
    'notification' => [
        'title' => 'ناتیفیکیشن',
        'icon' => 'fa fa-newspaper-o',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::NOTIFICATION_AFTER_LOGIN,
        ],
    ],
    'whatsapp' => [
        'title' => 'پیام در واتس اپ',
        'icon' => 'fa fa-whatsapp',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::WHATSAPP_MESSAGE_AFTER_LOGIN,
        ],
    ],
    'support' => [
        'title' => 'پشتیبانان',
        'icon' => 'fa fa-user',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::SUPPORT_USER_ROLE,
            \Modules\Setting\Enum\SettingKeyEnum::SUPPORTER_INTEREST,
        ],
    ],
    'payment' => [
        'title' => 'تنظیمات پرداخت',
        'icon' => 'fa fa-credit-card',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::PAYMEN_ACTIVE_DRIVER,
            \Modules\Setting\Enum\SettingKeyEnum::PAYMENT_PAYSTAR_TOKEN,
            \Modules\Setting\Enum\SettingKeyEnum::PAYMENT_PAYSTAR_SIGN,
            \Modules\Setting\Enum\SettingKeyEnum::PAYMENT_SAMAN_TERMINAL_NO,
            \Modules\Setting\Enum\SettingKeyEnum::PAYMENT_SAMAN_TERMINAL_PASS,
            \Modules\Setting\Enum\SettingKeyEnum::PAYMENT_ZARINPAL_MERCHENT
        ],
    ],
    'APP' => [
        'title' => 'تنظیمات اپ',
        'icon' => 'fa fa-gear',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::WEIGHT_CHART_DESCRIPTION_APP,
            \Modules\Setting\Enum\SettingKeyEnum::INVITE_FRIEND_BENEFIT,
            \Modules\Setting\Enum\SettingKeyEnum::INSTAGRAM,
            \Modules\Setting\Enum\SettingKeyEnum::FREE_RECHARGE,
            \Modules\Setting\Enum\SettingKeyEnum::APP_SHOPPING_LIST_ACTIVE,
            \Modules\Setting\Enum\SettingKeyEnum::APP_SHOPPING_LIST_ICON,
        ],
    ],
    'profile' => [
        'title' => 'پروفایل',
        'icon' => 'fa fa-gear',
        'settings' => [
            \Modules\Setting\Enum\SettingKeyEnum::PROFILE_FAQ_TITLE,
            \Modules\Setting\Enum\SettingKeyEnum::PROFILE_FAQ_PHONE,
            \Modules\Setting\Enum\SettingKeyEnum::PROFILE_FAQ_EMAIL,
        ],
    ],
];
