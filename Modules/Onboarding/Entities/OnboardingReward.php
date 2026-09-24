<?php

namespace Modules\Onboarding\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\PackageUser;
use Modules\User\Entities\User;

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
