<?php

namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Database\factories\InviteFrinedFactory;
use Modules\User\Entities\User;

class InviteFriend extends Model
{
    use HasFactory;

    protected $table = 'invite_friends';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userInvited(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class , 'user_invited_id');
    }

}
