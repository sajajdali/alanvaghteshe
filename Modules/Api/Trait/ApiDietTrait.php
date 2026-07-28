<?php

namespace Modules\Api\Trait;

trait ApiDietTrait
{
    private function calculatePercentage($referenceNumber, $secondNumber)
    {
        if ($referenceNumber == 0) {
            return 0; // جلوگیری از تقسیم بر صفر
        }

        $percentage = ($secondNumber / $referenceNumber) * 100;
        return (int) round(min(100, max(0, $percentage)));
    }

}
