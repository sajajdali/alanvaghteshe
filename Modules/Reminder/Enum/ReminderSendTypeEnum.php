<?php

namespace Modules\Reminder\Enum;

enum ReminderSendTypeEnum: int
{
    case SMS = 1;
    case NOTIFICATION = 2;
    case WHATSAPP = 3;

    public function getName(): string
    {
        return match($this) {
            self::SMS => 'پیامک',
            self::NOTIFICATION => 'ناتیفیکیشن',
            self::WHATSAPP => 'واتس اپ',
        };
    }

    public function getWireModelName(): string
    {
        return match($this)
        {
            self::SMS => 'sms' ,
            self::NOTIFICATION => 'notification' ,
            self::WHATSAPP => 'whatsapp' ,
            default => "",
        } ;
    }
}
