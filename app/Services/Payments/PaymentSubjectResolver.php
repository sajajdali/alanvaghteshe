<?php

namespace App\Services\Payments;

use DomainException;
use Illuminate\Http\Request;
use Modules\Coupon\Enum\CouponCanUsedForEnum;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseUser;
use Modules\Package\Entities\Package;
use Modules\Package\Enum\PackageTypeEnum;
use Modules\Transaction\Enum\TransactionPaymentForEnum;
use Modules\User\Entities\User;

class PaymentSubjectResolver
{
    public function resolve(Request $request, User $user): ResolvedPaymentSubject
    {
        if ($request->filled('course_id')) {
            return $this->resolveCourseRequest($request, $user);
        }

        return $this->resolvePackageRequest($request);
    }

    public function resolvePackageRequest(Request $request): ResolvedPaymentSubject
    {
        return $this->resolvePackage((int) $request->input('package_id'), [
            'diet_plan_id' => $request->input('diet_plan_id'),
            'target_plan' => $request->input('target_plan'),
            'user_weight' => $request->input('weight'),
        ]);
    }

    public function resolveCourseRequest(Request $request, User $user): ResolvedPaymentSubject
    {
        return $this->resolveCourse((int) $request->input('course_id'), $user);
    }

    private function resolvePackage(int $packageId, array $detail): ResolvedPaymentSubject
    {
        $package = Package::query()
            ->active()
            ->find($packageId);

        if (! $package) {
            throw new DomainException('پکیج انتخاب‌شده معتبر نیست.');
        }

        return new ResolvedPaymentSubject(
            type: 'package',
            model: $package,
            paymentFor: $this->resolveTransactionPaymentForPackage($package->type),
            couponCanUsedFor: $this->resolveCouponTargetForPackage($package->type),
            price: (int) $package->getPrice(),
            originalPrice: (int) $package->getOriginalPrice(),
            baseDiscount: (int) $package->getDiscountPackage(),
            detail: array_merge(['package_id' => $package->id], $detail),
        );
    }

    private function resolveCourse(int $courseId, User $user): ResolvedPaymentSubject
    {
        $course = Course::query()
            ->whereKey($courseId)
            ->where('is_active', true)
            ->where('is_published', true)
            ->where('is_purchasable', true)
            ->where(function ($query) {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->first();

        if (! $course) {
            throw new DomainException('دوره انتخاب‌شده معتبر نیست.');
        }

        $alreadyPurchased = CourseUser::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->whereNull('end_at')
                    ->orWhere('end_at', '>=', now()->toDateString());
            })
            ->exists();

        if ($alreadyPurchased) {
            throw new DomainException('این دوره را قبلاً خریداری کرده‌اید.');
        }

        $originalPrice = (int) ($course->price ?? 0);
        $finalPrice = $this->resolveCourseFinalPrice($course);

        return new ResolvedPaymentSubject(
            type: 'course',
            model: $course,
            paymentFor: TransactionPaymentForEnum::COURSE,
            couponCanUsedFor: CouponCanUsedForEnum::COURSE,
            price: $finalPrice,
            originalPrice: $originalPrice,
            baseDiscount: max(0, $originalPrice - $finalPrice),
            detail: [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_slug' => $course->slug,
                'source' => 'course',
            ],
        );
    }

    private function resolveCouponTargetForPackage(PackageTypeEnum $packageType): CouponCanUsedForEnum
    {
        return match ($packageType) {
            PackageTypeEnum::DIET,
            PackageTypeEnum::DIET_WITH_SUPPORT => CouponCanUsedForEnum::DIET,
            PackageTypeEnum::EXERCISE => CouponCanUsedForEnum::EXERCISE,
            PackageTypeEnum::DIET_AND_EXERCISE,
            PackageTypeEnum::DIET_AND_EXERCISE_WITH_SUPPORT => CouponCanUsedForEnum::DIET_AND_EXERCISE,
        };
    }

    private function resolveTransactionPaymentForPackage(PackageTypeEnum $packageType): TransactionPaymentForEnum
    {
        return match ($packageType) {
            PackageTypeEnum::DIET,
            PackageTypeEnum::DIET_WITH_SUPPORT => TransactionPaymentForEnum::DIET,
            PackageTypeEnum::EXERCISE => TransactionPaymentForEnum::EXERCISE,
            PackageTypeEnum::DIET_AND_EXERCISE,
            PackageTypeEnum::DIET_AND_EXERCISE_WITH_SUPPORT => TransactionPaymentForEnum::DIET_AND_EXERCISE,
        };
    }

    private function resolveCourseFinalPrice(Course $course): int
    {
        $price = (int) ($course->price ?? 0);
        $discountedPrice = $course->discounted_price !== null ? (int) $course->discounted_price : null;
        $finalPrice = (int) ($course->final_price ?? 0);

        if ($finalPrice > 0 || $price === 0) {
            return $finalPrice;
        }

        if ($discountedPrice !== null) {
            return $discountedPrice;
        }

        return $price;
    }
}
