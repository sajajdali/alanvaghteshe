<?php

namespace Modules\Reminder\app\Models;

use Modules\Reminder\Enum\ActiveEnum;
use Illuminate\Database\Eloquent\Model;
use Modules\Reminder\Enum\ReminderSendTypeEnum;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\Reminder\Enum\ReminderQueueModel;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    protected $casts = [
        'send_for'      => ReminderTypeEnum::class,
        'status'        => ReminderSendTypeEnum::class,
        'parameters'    => 'json',
        'detail'        => 'json',
        'active'        =>  ActiveEnum::class,
    ];


}
