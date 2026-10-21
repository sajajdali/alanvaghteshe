<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Api\Enum\RouteEnum;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Onboarding\Service\OnboardingService;
use Modules\User\Entities\User;

class LandingController extends Controller
{
    use ApiHandlerTrait;

    public function bmiResult()
    {
        return $this->ok(data: $this->getLandingData()
        );
    }

    private function getLandingData(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $userStartWeight = app('dietService')->getCurrentWeight($user);
        $targetWeight = $user->target_weight;
        $dayNeed = round(abs($userStartWeight - $targetWeight)) * 7;
        $bmi = app('dietService')->computeCalorie($user);
        $res = [
            'title' => 'برنامه تخصصی شما ساخته شد.',
            'order_diet' => [
                'title' => 'خرید برنامه رژیمی تخصصی',
                'button' => 'خرید برنامه',
                'description' => 'همین الان برنامه رژیمی تخصصی خودتون رو بگیرید و بریم برسیم به وزن هدف.',
            ],
            'onboarding_package' => $this->onboardingPackage($user),
            'start_weight' => (float) $userStartWeight,
            'target_weight' => (float) $targetWeight,
            'after_weight_plan' => 'با شرایطی که داری فقط توی '.$dayNeed.' روز میتونی به وزن ایده آلت برسی!',
            'calorie' => $bmi->calorie,
            'units' => $this->getDetailDiet($user),
        ];

        return $res;

    }

    private function onboardingPackage(User $user): ?array
    {
        $reward = app(OnboardingService::class)->activeReward($user);

        if (! $reward) {
            return null;
        }

        return [
            'package_user_id' => $reward->package_user_id,
            'package_id' => $reward->package_id,
            'title' => 'پکیج رایگان شما فعال است',
            'button' => 'ادامه و دریافت رژیم',
            'route' => RouteEnum::NEW_DIET->value,
            'description' => 'پکیج رایگان Onboarding برای شما فعال شده است. حالا می‌توانید ادامه دهید و رژیم خود را دریافت کنید.',
        ];
    }

    private function getDetailDiet($user)
    {
        return [
            'protein' => 30,
            'carb' => 30,
            'fat' => 30,
            'fiber' => 10,
        ];
    }
}
