<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Enum\RouteEnum;
use Modules\Onboarding\Service\OnboardingService;
use RuntimeException;

class OnboardingController extends Controller
{
    use ApiHandlerTrait;

    public function show(OnboardingService $service): JsonResponse
    {
        return $this->ok($service->payload(auth()->user()));
    }

    public function activateFreePackage(OnboardingService $service): JsonResponse
    {
        try {
            $packageUser = $service->activateFreePackage(auth()->user());
        } catch (RuntimeException $exception) {
            return $this->badRequest(['message' => $exception->getMessage()]);
        }

        return $this->created([
            'message' => 'پکیج رایگان با موفقیت فعال شد.',
            'next_route' => RouteEnum::PROFILE->value,
            'package' => [
                'package_user_id' => $packageUser->getKey(),
                'package_id' => $packageUser->package_id,
                'start_at' => $packageUser->start_at,
                'end_at' => $packageUser->end_at,
            ],
        ]);
    }
}
