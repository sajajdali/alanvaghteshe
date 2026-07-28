<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Core\Entities\Disease
 *
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property int $priority
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Disease> $children
 * @property-read int|null $children_count
 * @property-read Disease|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\User\Entities\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Disease active()
 * @method static \Modules\Core\Database\factories\DiseaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Disease inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|Disease newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Disease newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Disease parent()
 * @method static \Illuminate\Database\Eloquent\Builder|Disease priority()
 * @method static \Illuminate\Database\Eloquent\Builder|Disease query()
 * @method static \Illuminate\Database\Eloquent\Builder|Disease whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Disease whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Disease whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Disease whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Disease wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Disease whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Disease whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Disease extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function newFactory(): \Modules\Core\Database\factories\DiseaseFactory
    {
        return \Modules\Core\Database\factories\DiseaseFactory::new();
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', true);
    }

    public function scopeInactive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', false);
    }

    public function scopePriority(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    public function scopeParent(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('parent_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Disease::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Disease::class, 'parent_id');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\Modules\User\Entities\User::class, 'diseases_users', 'disease_id', 'user_id');
    }


}
