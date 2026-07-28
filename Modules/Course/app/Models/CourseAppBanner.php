<?php

namespace Modules\Course\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseAppBanner extends Model
{
    use HasFactory;

    public const POSITION_TOP = 'top';
    public const POSITION_MIDDLE = 'middle';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function positions(): array
    {
        return [
            self::POSITION_TOP => 'بالای اپلیکیشن',
            self::POSITION_MIDDLE => 'وسط اپلیکیشن',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getPositionLabelAttribute(): string
    {
        return self::positions()[$this->position] ?? $this->position;
    }
}
