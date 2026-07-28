<?php

namespace App\Services\Payments;

use Modules\Coupon\Entities\Coupon;
use Modules\User\Entities\User;

class PaymentPricingService
{
    public function calculateWithoutCoupon(ResolvedPaymentSubject $subject, User $user): array
    {
        return $this->buildPricingResult($subject->price, 0, $user);
    }

    public function calculateWithCoupon(?Coupon $coupon, ResolvedPaymentSubject $subject, User $user): array
    {
        if (! $coupon) {
            return [
                'status' => false,
                'message' => 'کدی یافت نشد.',
            ];
        }

        if ($coupon->start_at && $coupon->start_at->isFuture()) {
            return [
                'status' => false,
                'message' => 'زمان استفاده از کد فرا نرسیده است.',
            ];
        }

        if ($coupon->end_at && $coupon->end_at->isPast()) {
            return [
                'status' => false,
                'message' => 'زمان استفاده از کد گذشته است.',
            ];
        }

        if ($coupon->can_used_for !== $subject->couponCanUsedFor) {
            return [
                'status' => false,
                'message' => 'این کد تخفیف برای این پرداخت قابل استفاده نیست.',
            ];
        }

        if ($coupon->minimum_spend && $coupon->minimum_spend > $subject->price) {
            return [
                'status' => false,
                'message' => 'این کد تخفیف برای این مبلغ قابل استفاده نیست.',
            ];
        }

        if ($coupon->maximum_spend && $coupon->maximum_spend < $subject->price) {
            return [
                'status' => false,
                'message' => 'این کد تخفیف برای این مبلغ قابل استفاده نیست.',
            ];
        }

        if ($coupon->usable_count && $coupon->used >= $coupon->usable_count) {
            return [
                'status' => false,
                'message' => 'محدودیت استفاده این کد تخفیف به اتمام رسیده است.',
            ];
        }

        $discount = 0;

        if ($coupon->is_percent && $coupon->value > 0) {
            $discount = (int) round($subject->price * ($coupon->value / 100));
        } elseif (! $coupon->is_percent && $coupon->value > 0) {
            $discount = min((int) $coupon->value, $subject->price);
        }

        return $this->buildPricingResult(
            amountBeforeWallet: max(0, $subject->price - $discount),
            discount: $discount,
            user: $user,
            couponId: $coupon->id,
            message: 'کد تخفیف با موفقیت اعمال شد.',
        );
    }

    private function buildPricingResult(
        int $amountBeforeWallet,
        int $discount,
        User $user,
        ?int $couponId = null,
        string $message = 'ok',
    ): array {
        $walletAmount = max(0, (int) ($user->wallet ?? 0));
        $walletContribution = min($walletAmount, $amountBeforeWallet);

        return [
            'status' => true,
            'message' => $message,
            'coupon_id' => $couponId,
            'amount' => max(0, $amountBeforeWallet - $walletContribution),
            'discount' => $discount,
            'required_amount_of_wallet' => $walletContribution,
        ];
    }
}
