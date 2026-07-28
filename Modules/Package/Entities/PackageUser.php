<?php

namespace Modules\Package\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Modules\Package\Enum\PackageUserTypeEnum;

/**
 * Modules\Package\Entities\PackageUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $package_id
 * @property string|null $start_at
 * @property string|null $end_at
 * @property PackageUserTypeEnum $type 0 = pending | 1 = in_use | 2 = end | 3 = cancel
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $past_days
 * @property-read int $remaining_days
 * @property-read \Modules\Package\Entities\Package $package
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageUser whereUserId($value)
 * @mixin \Eloquent
 */
class PackageUser extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'type' => PackageUserTypeEnum::class,
        'in_use'    => 'boolean'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getPastDaysAttribute(): int
    {
        return now()->diffInDays($this->start_at);
    }

    public function getRemainingDaysAttribute(): int
    {
        return $this->package->days - $this->past_days;
    }

}
