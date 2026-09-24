<?php

namespace Modules\Onboarding\Service;

use Illuminate\Support\Facades\DB;
use Modules\Api\Enum\RouteEnum;
use Modules\Onboarding\Entities\OnboardingReward;
use Modules\Onboarding\Entities\OnboardingSetting;
use Modules\Onboarding\Enum\OnboardingModeEnum;
use Modules\Package\Entities\PackageUser;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\User\Entities\User;
use RuntimeException;

class OnboardingService
{
    /** @return array{show_onboarding: bool, next_route: string} */
    public function decision(User $user): array
    {
        $setting = OnboardingSetting::current();
        $isMember = $user->activePackage() !== null;

        if ($user->weight === null) {
            return [
                'show_onboarding' => false,
                'next_route' => RouteEnum::REGISTER->value,
            ];
        }

        return [
            'show_onboarding' => $setting->is_active && ! $isMember,
            'next_route' => $setting->is_active && ! $isMember
                ? RouteEnum::ONBOARDING->value
                : RouteEnum::PROFILE->value,
        ];
    }

    /** @return array<string, mixed> */
    public function payload(User $user): array
    {
        $setting = OnboardingSetting::current()->load(['package', 'coupon']);
        $decision = $this->decision($user);

        if (! $decision['show_onboarding']) {
            return $decision + [
                'mode' => null,
                'content' => null,
                'offer' => null,
            ];
        }

        $mode = $setting->mode;
        $content = $setting->getContentsWithDefaults()[$mode->value];
        $content = $this->replaceVariables(
            $content,
            $user,
            $setting->package?->days
        );

        return $decision + [
            'mode' => $mode->value,
            'content' => $content,
            'offer' => match ($mode) {
                OnboardingModeEnum::COUPON => $setting->coupon ? [
                    'coupon_id' => $setting->coupon->getKey(),
                    'code' => $setting->coupon->code,
                    'value' => (int) $setting->coupon->value,
                    'is_percent' => (bool) $setting->coupon->is_percent,
                ] : null,
                OnboardingModeEnum::FREE_PACKAGE => $setting->package ? [
                    'package_id' => $setting->package->getKey(),
                    'name' => $setting->package->name,
                    'days' => (int) $setting->package->days,
                    'type' => $setting->package->type->value,
                    'type_name' => $setting->package->type->getName(),
                    'already_claimed' => OnboardingReward::query()->where('user_id', $user->getKey())->exists(),
                ] : null,
                OnboardingModeEnum::NONE => null,
            },
        ];
    }

    public function activateFreePackage(User $user): PackageUser
    {
        if ($user->activePackage()) {
            throw new RuntimeException('کاربر در حال حاضر عضویت فعال دارد.');
        }

        $setting = OnboardingSetting::current()->load('package');
        if (! $setting->is_active || $setting->mode !== OnboardingModeEnum::FREE_PACKAGE || ! $setting->package) {
            throw new RuntimeException('پکیج رایگان Onboarding فعال نیست.');
        }

        return DB::transaction(function () use ($user, $setting): PackageUser {
            $lockedUser = User::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedUser->activePackage()) {
                throw new RuntimeException('کاربر در حال حاضر عضویت فعال دارد.');
            }

            if (OnboardingReward::query()->where('user_id', $user->getKey())->lockForUpdate()->exists()) {
                throw new RuntimeException('این پکیج رایگان قبلاً برای کاربر فعال شده است.');
            }

            $packageUser = $lockedUser->packages()->create([
                'package_id' => $setting->package->getKey(),
                'start_at' => now()->toDateString(),
                'end_at' => now()->addDays($setting->package->days)->toDateString(),
                'type' => PackageUserTypeEnum::IN_USE,
            ]);

            OnboardingReward::query()->create([
                'user_id' => $user->getKey(),
                'package_id' => $setting->package->getKey(),
                'package_user_id' => $packageUser->getKey(),
            ]);

            return $packageUser;
        });
    }

    /** @param array<string, string> $content */
    private function replaceVariables(array $content, User $user, ?int $days): array
    {
        return array_map(
            static fn (string $text): string => str_replace(
                ['{name}', '{days}'],
                [$user->first_name ?: 'دوست عزیز', (string) ($days ?? '')],
                $text
            ),
            $content
        );
    }
}
