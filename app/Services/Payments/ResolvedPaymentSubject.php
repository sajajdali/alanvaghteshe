<?php

namespace App\Services\Payments;

use Modules\Coupon\Enum\CouponCanUsedForEnum;
use Modules\Course\app\Models\Course;
use Modules\Package\Entities\Package;
use Modules\Transaction\Enum\TransactionPaymentForEnum;

class ResolvedPaymentSubject
{
    public function __construct(
        public readonly string $type,
        public readonly Package|Course $model,
        public readonly TransactionPaymentForEnum $paymentFor,
        public readonly CouponCanUsedForEnum $couponCanUsedFor,
        public readonly int $price,
        public readonly int $originalPrice,
        public readonly int $baseDiscount,
        public readonly array $detail,
    ) {
    }

    public function isPackage(): bool
    {
        return $this->type === 'package';
    }

    public function isCourse(): bool
    {
        return $this->type === 'course';
    }

    public function toDiscountResponse(int $payableAmount): array
    {
        if ($this->isPackage()) {
            return [
                'package' => getPackage($this->model->id, $payableAmount),
            ];
        }

        /** @var Course $course */
        $course = $this->model;

        return [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'thumbnail' => $course->thumbnail,
                'price' => $this->originalPrice,
                'final_price' => $payableAmount,
            ],
        ];
    }
}
