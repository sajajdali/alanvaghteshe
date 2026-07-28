<?php

namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Database\factories\MessageFactory;
use Modules\User\Entities\User;

class Message extends Model
{


    protected $guarded = ['id'];
    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
