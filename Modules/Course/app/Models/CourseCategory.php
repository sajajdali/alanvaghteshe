<?php

namespace Modules\Course\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function getFullTitleAttribute(): string
    {
        return $this->parent ? ($this->parent->title . ' / ' . $this->title) : $this->title;
    }

    public function getDepthAttribute(): int
    {
        return $this->parent_id ? 2 : 1;
    }
}
