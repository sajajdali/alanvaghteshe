<?php

namespace Modules\Onboarding\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\PackageUser;
use Modules\User\Entities\User;

/**
 * @property int $id
 * @property int $user_id
 * @property int $package_id
 * @property int $package_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Package $package
 * @property-read PackageUser $packageUser
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward wherePackageUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingReward whereUserId($value)
 * @mixin \Eloquent
 */
class OnboardingReward extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function packageUser(): BelongsTo
    {
        return $this->belongsTo(PackageUser::class);
    }
}
