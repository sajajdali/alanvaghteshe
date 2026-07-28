<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Core\Entities\Faq
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property bool $status
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Faq active()
 * @method static \Modules\Core\Database\factories\FaqFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Faq inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq priority()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Faq extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function newFactory(): \Modules\Core\Database\factories\FaqFactory
    {
        return \Modules\Core\Database\factories\FaqFactory::new();
    }


    public function scopeActive($query)
    {
        return $query->whereStatus(true);
    }

    public function scopeInactive($query)
    {
        return $query->whereStatus(false);
    }

    public function scopePriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
