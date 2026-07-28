<?php

namespace Modules\Course\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionPaidEnum;
use Modules\User\Entities\User;

class CourseUser extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'paid_by' => TransactionPaidEnum::class,
        'is_active' => 'boolean',
        'start_at' => 'date',
        'end_at' => 'date',
        'detail' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_admin_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseLessonProgress::class);
    }

    public function getProgressPercentAttribute(): int
    {
        $totalLessons = (int) ($this->course?->lessons_count ?? $this->course?->lessons()->count() ?? 0);
        if ($totalLessons === 0) {
            return 0;
        }

        $completed = (int) ($this->progress_count ?? $this->progress()->count());

        return (int) floor(($completed / $totalLessons) * 100);
    }

    public function getRemainingPercentAttribute(): int
    {
        return max(0, 100 - $this->progress_percent);
    }
}
