<?php

namespace Modules\Course\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const LEVEL_EASY = 1;
    public const LEVEL_MEDIUM = 2;
    public const LEVEL_PROFESSIONAL = 3;

    protected $guarded = ['id'];

    protected $casts = [
        'is_latest' => 'boolean',
        'is_popular' => 'boolean',
        'level' => 'integer',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'is_purchasable' => 'boolean',
        'published_at' => 'datetime',
    ];

    public static function maxOrder(): int
    {
        return (int) self::max('sort_order') + 1;
    }

    public static function levels(): array
    {
        return [
            self::LEVEL_EASY => 'آسان',
            self::LEVEL_MEDIUM => 'متوسط',
            self::LEVEL_PROFESSIONAL => 'حرفه‌ای',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function getLevelLabelAttribute(): string
    {
        return self::levels()[$this->level] ?? 'نامشخص';
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(CourseLesson::class, CourseSection::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(CourseFaq::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CourseComment::class)->latest();
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(CourseUser::class)->latest();
    }
}
