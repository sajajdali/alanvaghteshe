<?php

namespace Modules\Setting\Enum;

use App\interface\EnumHasNameInterface;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Setting\Interface\SettingHasCacheInterface;
use Modules\Setting\Interface\SettingHasOptionInterface;
use Modules\Setting\Interface\SettingRenderAbleInterface;
use Modules\Setting\Interface\SettingTypeInterface;
use Modules\User\Entities\User;
use phpDocumentor\Reflection\Types\Self_;

enum SettingKeyEnum: int implements EnumHasNameInterface, SettingTypeInterface, SettingHasCacheInterface, SettingRenderAbleInterface, SettingHasOptionInterface
{
    case DEFAULT_EXERCISE_STATUS = 1;
    case SMS_API_TOKEN = 20;
    case SMS_API_LOGIN_TEMPLATE = 30;
    case SMS_AFTER_SEND_DIET = 31;
    case SMS_AFTER_ORDER_PACKAGE = 144;
    case SMS_SEND_APP_LINK = 145;
    case SMS_AFTER_ORDER_COURSE = 166;
    case SUPPORT_USER_ROLE = 100;
    case PAYMENT_PAYSTAR_TOKEN = 150;
    case PAYMENT_PAYSTAR_SIGN = 151;
    case WEIGHT_CHART_DESCRIPTION_APP = 120;
    case PROFILE = 130;
    case PROFILE_FAQ_PHONE = 131;
    case PROFILE_FAQ_EMAIL = 132;
    case PROFILE_FAQ_TITLE = 133;
    case INVITE_FRIEND_BENEFIT = 140;
    case INSTAGRAM = 141;
    case FREE_RECHARGE = 142;
    case NOTIFICATION_AFTER_LOGIN = 143;
    case PAYMEN_ACTIVE_DRIVER = 160;
    case PAYMENT_SAMAN_TERMINAL_NO = 161;
    case PAYMENT_SAMAN_TERMINAL_PASS = 162;
    case PAYMENT_ZARINPAL_MERCHENT = 164;
    case SUPPORTER_INTEREST = 163;

    case WHATSAPP_MESSAGE_AFTER_LOGIN = 165;

    public function isSupportCache(): bool
    {
        return match ($this) {
            self::SMS_API_TOKEN => false,
            self::SUPPORT_USER_ROLE => false,
            self::SMS_API_LOGIN_TEMPLATE => false,
            self::PAYMENT_PAYSTAR_TOKEN => false,
            self::PROFILE => false,
            self::PAYMENT_PAYSTAR_SIGN => false,
            self::WEIGHT_CHART_DESCRIPTION_APP => false,
            self::INVITE_FRIEND_BENEFIT => false,
            default => true
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::PAYMEN_ACTIVE_DRIVER => 'درگاه فعال',
            self::SMS_API_TOKEN => 'توکن API پیامک',
            self::DEFAULT_EXERCISE_STATUS => 'وضعیت برنامه بعد از تجویز',
            self::SUPPORT_USER_ROLE => 'گروه کاربری پشتیبانان',
            self::SMS_API_LOGIN_TEMPLATE => 'الگو پیامک ورود',
            self::SMS_AFTER_SEND_DIET => 'الگو پیامک پس از ارسال رژیم',
            self::SMS_AFTER_ORDER_PACKAGE => 'الگو پیامک پس از خرید پکیج',
            self::SMS_AFTER_ORDER_COURSE => 'الگو پیامک پس از خرید دوره',
            self::SMS_SEND_APP_LINK => 'الگو پیامک ارسال لینک اپلیکیشن',
            self::PAYMENT_PAYSTAR_TOKEN => 'کد درگاه پرداخت پی استار',
            self::PROFILE => 'تنظیمات بخش پروفایل',
            self::PROFILE_FAQ_TITLE => ' عنوان در faq',
            self::PROFILE_FAQ_PHONE => 'شماره تماس با ما در faq',
            self::PROFILE_FAQ_EMAIL => ' ایمیل در faq',
            self::PAYMENT_PAYSTAR_SIGN => 'امضا درگاه پی استار',
            self::WEIGHT_CHART_DESCRIPTION_APP => 'متن توضیح در صفحه ی مشاهده مودار وزنی ',
            self::INVITE_FRIEND_BENEFIT => 'مبلغ مورد نظر به کاربر پس از معرفی هر یوزر',
            self::INSTAGRAM => 'آدرس پیج اینستاگرام',
            self::FREE_RECHARGE => 'شارژ رایگان (ریال)',
            self::NOTIFICATION_AFTER_LOGIN => 'پس از عضویت کاربر',
            self::WHATSAPP_MESSAGE_AFTER_LOGIN => 'پیام در واتس اپ پس از عضویت',
            self::PAYMENT_SAMAN_TERMINAL_NO => 'شماره ترمینال درگاه سامان',
            self::PAYMENT_SAMAN_TERMINAL_PASS => ' رمز عبور ترمینال درگاه سامان',
            self::PAYMENT_ZARINPAL_MERCHENT => 'مرچنت درگاه زرین پال',
            self::SUPPORTER_INTEREST => 'درصد دریافتی پشتیبان بعد از انجام خرید توسط کاربر(مثلا 10)',
            default => ''
        };
    }

    public function render(): string
    {
        return $this->getType()->component($this->value);
    }

    public function getType(): SettingTypeEnum
    {
        return match ($this) {
            self::DEFAULT_EXERCISE_STATUS       => SettingTypeEnum::SELECT,
            self::SUPPORT_USER_ROLE             => SettingTypeEnum::SELECT,
            self::WEIGHT_CHART_DESCRIPTION_APP ,self::NOTIFICATION_AFTER_LOGIN , self::WHATSAPP_MESSAGE_AFTER_LOGIN => SettingTypeEnum::TEXTAREA,
            self::PAYMEN_ACTIVE_DRIVER          => SettingTypeEnum::SELECT,
            default                             => SettingTypeEnum::TEXT,
        };
    }

    public function getDescription()
    {
        return match ($this) {
            self::SMS_AFTER_ORDER_PACKAGE => 'پارامتر ها به ترتیب به شکل زیر باشد:
            <br />  ۱ = نام کاربر
             ',
            self::SMS_AFTER_ORDER_COURSE => 'پارامتر ها به ترتیب به شکل زیر باشد:
            <br />  ۱ = نام کاربر
             ',
            default => ''
        };
    }
    /**
     * Radio, select and checkbox options
     */
    public function options(): array
    {
        return match ($this) {
//            self::DEFAULT_EXERCISE_STATUS => ExercisePlanRequest::getArrayForSetting(),
            self::SUPPORT_USER_ROLE => User::adminSupportRoles(),
            self::PAYMEN_ACTIVE_DRIVER => ['paystart' => 'paystart', 'saman' => 'saman' , 'zarinpal' => 'zarinpal'],
            default => []
        };
    }
}
