<?php

namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class WalletTransaction extends Model
{

    protected $casts = [
        'detail' => 'json'
    ];
    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
