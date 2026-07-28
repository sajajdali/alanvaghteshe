<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Api\Trait\ApiHandlerTrait;

class LandingController extends Controller
{
    use ApiHandlerTrait;
    public function bmiResult()
    {
        return $this->ok(data:
             $this->getLandingData()
        );
    }

    private function getLandingData(): array
    {
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
                'description' => 'کیف پولتون به عنوان هدیه ۲۰ هزار تومان شارژ شد!. همین الان برنامه رژیمی خودتون رو با این هدیه بگیرید و بریم برسیم به وزن هدف',
            ],
            'start_weight' => (float) $userStartWeight,
            'target_weight' => (float) $targetWeight,
            'after_weight_plan' => 'با شرایطی که داری فقط توی ' . $dayNeed . ' روز میتونی به وزن ایده آلت برسی!',
            'calorie' => $bmi->calorie,
            'units' => $this->getDetailDiet($user),
        ];
        return $res;

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
