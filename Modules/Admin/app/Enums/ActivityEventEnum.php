<?php

namespace Modules\Admin\app\Enums;

enum ActivityEventEnum: string
{
    case SEND_APP_SMS_LINK = 'send_app_sms_link';
    case CALL_ONEDAY_LEFT_PACKAGE_EXPIRED = 'call_oneDay_expired_package';
    case CALL_EX_PACKAGE = 'call_expired_package';
    case CALL_EX_PACKAGE_TEN_DAYS_PASS = 'call_expired_package_ten_days_pass';
    case CALL_EX_PACKAGE_MONTH_DAYS_PASS = 'call_expired_package_month_days_pass';

    //diets
    case DIET_END_ONE_DAY = 'diet_end_one_day';
    case DIET_START_SEVEN_DAYS_AGO = 'diet_start_seven_days_ago';
    case DIET_START_FOURTEEN_DAYS_AGO = 'diet_start_fourteen_days_ago';
    case DIET_END_YESTERDAY = 'diet_end_yesterday';
    case DIET_END_ONE_WEEK_AGO = 'diet_end_one_week_ago';
    case ADD_PROFILE_NOTE = 'add_profile_note';

    public function getName(): string
    {
        return match ($this) {
            self::SEND_APP_SMS_LINK => 'ارسال لینک اپلیکیشن',
            self::CALL_ONEDAY_LEFT_PACKAGE_EXPIRED => 'تماس پشتیبانی: یک روز تا پایان پکیج',
            self::CALL_EX_PACKAGE => 'تماس پشتیبانی: اتمام پکیج و عدم تمدید',
            self::CALL_EX_PACKAGE_TEN_DAYS_PASS => ' تماس پشتیبانی: اتمام پکیج و عدم تمدید تا 10 روز',
            self::CALL_EX_PACKAGE_MONTH_DAYS_PASS => ' تماس پشتیبانی: اتمام پکیج و عدم تمدید تا یک ماه',

            self::DIET_END_ONE_DAY =>  'کسانی که امروز رژیمشان تمام میشود',
            self::DIET_START_SEVEN_DAYS_AGO =>  'کسانی که ۷ روز از رژیمشان گذشته است',
            self::DIET_START_FOURTEEN_DAYS_AGO =>  'کسانی که ۱۴ روز از رژیمشان گذشته است',
            self::DIET_END_YESTERDAY =>  'کسانی که دیروز رژیمشان تمام شده است',
            self::DIET_END_ONE_WEEK_AGO =>  'کسانی که یک هفته است که رژیمشان تمام شده است',
            self::ADD_PROFILE_NOTE =>  'ثبت تماس دستی در پروفایل کاربر',
        };
    }
}
