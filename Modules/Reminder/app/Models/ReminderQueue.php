<?php

namespace Modules\Reminder\app\Models;
use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Reminder\Database\factories\ReminderQueueFactory;

class ReminderQueue extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    protected $casts = [
        'type' => \Modules\Reminder\Enum\ReminderTypeEnum::class,
        'status' => \Modules\Reminder\Enum\ReminderStatusEnum::class,
        'detail'         => 'json',
    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
}
