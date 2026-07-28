<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSession extends Model
{
    protected $guarded = ['id'];
    //

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }
}
