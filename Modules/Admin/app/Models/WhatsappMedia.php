<?php

namespace Modules\Admin\app\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WhatsappMedia extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'size'        => 'integer',
        'width'       => 'integer',
        'height'      => 'integer',
        'duration_ms' => 'integer',
    ];

    /*
     |--------------------------------------------------------------------------
     | روابط
     |--------------------------------------------------------------------------
     */

    public function message()
    {
        return $this->belongsTo(WhatsappMessage::class, 'message_id');
    }

    /*
     |--------------------------------------------------------------------------
     | اکسسورها و متدهای کاربردی
     |--------------------------------------------------------------------------
     */

    /**
     * URL عمومی فایل (از storage disk)
     */
    public function getUrlAttribute(): ?string
    {
        if ($this->public_url) {
            return $this->public_url;
        }

        try {
            return Storage::disk($this->disk ?: 'public')->url($this->path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * بررسی اینکه فایل وجود دارد یا نه
     */
    public function existsOnDisk(): bool
    {
        return Storage::disk($this->disk ?: 'public')->exists($this->path);
    }

    /**
     * حذف فیزیکی فایل همراه با رکورد
     */
    protected static function booted()
    {
        static::deleting(function (self $media) {
            if ($media->existsOnDisk()) {
                Storage::disk($media->disk ?: 'public')->delete($media->path);
            }
        });
    }
}
