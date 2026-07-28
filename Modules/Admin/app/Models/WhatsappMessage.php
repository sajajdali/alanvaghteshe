<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class WhatsappMessage extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'from_me'       => 'boolean',
        'has_media'     => 'boolean',
        'read_at'       => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | روابط
     |--------------------------------------------------------------------------
     */

    // ارتباط با کاربر (در صورت وجود)
    public function user()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class);
    }

    // هر پیام می‌تواند چند رسانه داشته باشد
    public function media()
    {
        return $this->hasMany(WhatsappMedia::class, 'message_id');
    }

    /*
     |--------------------------------------------------------------------------
     | متدهای کمکی
     |--------------------------------------------------------------------------
     */

    public function scopeFromMe($query)
    {
        return $query->where('from_me', true);
    }

    public function scopeToMe($query)
    {
        return $query->where('from_me', false);
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isMedia(): bool
    {
        return $this->has_media === true;
    }

    public function latestMedia()
    {
        return $this->media()->latest()->first();
    }

    public function getFormattedBodyAttribute(): ?string
    {
        return $this->body ? trim(strip_tags($this->body)) : null;
    }

    public function session(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WhatsappSession::class);
    }
}
