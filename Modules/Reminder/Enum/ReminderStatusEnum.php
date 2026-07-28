<?php

namespace Modules\Reminder\Enum;

enum ReminderStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case CANCELLED = 'cancelled';

    public function getName(): string
    {
        return match($this) {
            self::PENDING => 'در انتظار ارسال',
            self::SENT => 'ارسال شده',
            self::CANCELLED => 'لغو شده',
        };
    }


}
