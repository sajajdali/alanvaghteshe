<?php

namespace App\Services\Payments;

use Carbon\Carbon;
use Modules\Diet\Entities\DietPlan;
use Modules\Package\Entities\Package;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionPaymentForEnum;
use Modules\Transaction\Enum\TransactionStatusEnum;
use Modules\User\app\Notifications\SmsNotification;
use Modules\User\Enum\UserMetaEnum;
use Modules\User\Http\Controllers\WalletController;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseUser;

class TransactionFinalizer
{
    public function complete(Transaction $transaction): void
    {
        $detail = $transaction->detail ?? [];

        if (! empty($detail['fulfilled_at'])) {
            return;
        }

        $this->markSuccessful($transaction);
        $this->recordCouponUsage($transaction);
        $this->assignPurchasedItem($transaction);
        $this->sendOrderSms($transaction);
        $this->debitWallet($transaction);

        $detail = $transaction->fresh()->detail ?? [];
        $detail['fulfilled_at'] = now()->toDateTimeString();

        $transaction->update([
            'detail' => $detail,
        ]);
    }

    private function markSuccessful(Transaction $transaction): void
    {
        if ($transaction->status !== TransactionStatusEnum::SUCCESSFUL) {
            $transaction->update([
                'status' => TransactionStatusEnum::SUCCESSFUL,
            ]);
        }
    }

    private function recordCouponUsage(Transaction $transaction): void
    {
        $detail = $transaction->detail ?? [];

        if ($transaction->coupon && empty($detail['coupon_usage_recorded_at'])) {
            $transaction->coupon->increment('used');
            $detail['coupon_usage_recorded_at'] = now()->toDateTimeString();
            $transaction->update(['detail' => $detail]);
        }
    }

    private function assignPurchasedItem(Transaction $transaction): void
    {
        if ($transaction->payment_for === TransactionPaymentForEnum::COURSE) {
            $this->assignCourseToUser($transaction);

            return;
        }

        $this->assignPackageToUser($transaction);
    }

    private function assignCourseToUser(Transaction $transaction): void
    {
        $detail = $transaction->detail ?? [];

        if (! empty($detail['course_purchase_id'])) {
            return;
        }

        $course = Course::find($detail['course_id'] ?? null);

        if (! $course) {
            return;
        }

        $purchase = CourseUser::query()->firstOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'user_id' => $transaction->user_id,
                'course_id' => $course->id,
                'paid_by' => $transaction->paid_by->value,
                'start_at' => now()->toDateString(),
                'end_at' => $course->access_days > 0 ? now()->addDays($course->access_days)->toDateString() : null,
                'is_active' => true,
                'detail' => [
                    'payment_source' => 'api_payment',
                ],
            ]
        );

        $detail['course_purchase_id'] = $purchase->id;
        $transaction->update(['detail' => $detail]);
    }

    private function assignPackageToUser(Transaction $transaction): void
    {
        $detail = $transaction->detail ?? [];

        if (! empty($detail['package_assigned_at'])) {
            return;
        }

        $package = Package::find($detail['package_id'] ?? null);
        $user = $transaction->user;

        if (! $package || ! $user) {
            return;
        }

        $user->packages()
            ->where('type', PackageUserTypeEnum::IN_USE)
            ->update(['type' => PackageUserTypeEnum::CANCEL]);

        $user->packages()->create([
            'package_id' => $package->id,
            'start_at' => Carbon::now()->toDateString(),
            'end_at' => Carbon::now()->addDays($package->days ?? 0)->toDateString(),
            'type' => PackageUserTypeEnum::IN_USE,
        ]);

        $dietPlanModel = DietPlan::find($detail['diet_plan_id'] ?? null);
        if ($dietPlanModel) {
            app('dietService')->generateDiet($user, $dietPlanModel, $detail['target_plan'] ?? null);
        }

        $user->metas()->where('meta_key', UserMetaEnum::POPUP)->delete();
        $user->dispatchPaymentReminders($package->days);

        $detail['package_assigned_at'] = now()->toDateTimeString();
        $transaction->update(['detail' => $detail]);
    }

    private function sendOrderSms(Transaction $transaction): void
    {
        $user = $transaction->user;

        if (! $user || ! isset($user->first_name)) {
            return;
        }

        $settingKey = $transaction->payment_for === TransactionPaymentForEnum::COURSE
            ? SettingKeyEnum::SMS_AFTER_ORDER_COURSE
            : SettingKeyEnum::SMS_AFTER_ORDER_PACKAGE;

        $template = setting($settingKey);

        if (! $template && $transaction->payment_for === TransactionPaymentForEnum::COURSE) {
            $template = setting(SettingKeyEnum::SMS_AFTER_ORDER_PACKAGE);
        }

        if (! $template) {
            return;
        }

        $user->notify(new SmsNotification($template, [$user->first_name]));
    }

    private function debitWallet(Transaction $transaction): void
    {
        $detail = $transaction->detail ?? [];
        $walletAmount = (int) ($detail['user_wallet'] ?? 0);

        if ($walletAmount <= 0 || ! empty($detail['wallet_debited_at'])) {
            return;
        }

        $user = $transaction->user;

        if (! $user) {
            return;
        }

        WalletController::debit($user, $walletAmount, [
            'change_wallet_charge_by' => $user->id,
            'transaction_id' => $transaction->id,
        ]);

        $detail['wallet_debited_at'] = now()->toDateTimeString();
        $transaction->update(['detail' => $detail]);
    }
}
