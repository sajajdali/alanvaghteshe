<?php

namespace App\Services;

use App\Services\Payments\TransactionFinalizer;
use App\UserSession;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Modules\Package\Entities\Package;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\Services\WalletService;
use Modules\Package\Enum\PackageTypeEnum;
use Modules\User\app\Models\InviteFriend;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionStatusEnum;
use App\Notifications\OrderTelegramMessageNotification;
use Shetabit\Payment\Facade\Payment as ShetabitPayment;

class PaymentVerifyService
{
    /**
     * Verify and finalize a transaction.
     * Returns array: ['success' => bool, 'message' => string|null]
     */
    public function verifyAndFinalize(Transaction $transaction, string $provider, array $input = []): array
    {
        // Idempotency: اگر قبلاً انجام شده، کاری نکن
        if ($transaction->status === TransactionStatusEnum::SUCCESSFUL) {
            return ['success' => true, 'message' => 'already_successful'];
        }

        $amount = $transaction->total_cost;

        if ($provider === 'saman') {
            $result = $this->verifySaman($transaction, $amount, $input);
        } elseif ($provider === 'paystar') {
            $result = $this->verifyPaystar($transaction, $amount, $input);
        } else {
            return ['success' => false, 'message' => 'unknown_provider'];
        }

        if (!($result['success'] ?? false)) {
            return ['success' => false, 'message' => $result['message'] ?? 'verify_failed', 'details' => $result['details'] ?? null];
        }

        $detail = $transaction->detail ?? [];
        $detail['gateway_details'] = $result['details'] ?? [];
        if (!empty($result['reference'])) $detail['ref_id'] = $result['reference'];

        $transaction->update([
            'detail' => $detail,
        ]);

        app(TransactionFinalizer::class)->complete($transaction);

        if ($transaction->total_cost > 0) {
            $this->handleReferral($transaction);
            $this->handleSupporterInterest($transaction->user, $transaction);
        }

        return ['success' => true, 'message' => 'ok'];
    }

    private function handleSupporterInterest(User $user, Transaction $transaction): void
    {
        if ($transaction->total_cost == 0) {
            return;
        }
        if ($user->lastSupporterCalled == "null") {
            return;
        }
        $supporterInterest = (int) setting(\Modules\Setting\Enum\SettingKeyEnum::SUPPORTER_INTEREST);
        if ($supporterInterest  == 0) {
            return;
        }
        try {
            $supporter = User::find($user->lastSupporterCalled);
            $amount = (int) ($transaction->total_cost * $supporterInterest) / 100;
            $detail['interest_from'] = [
                'user'                => $user->id,
                'transaction'         => $transaction->id,
                'total'               => $transaction->total_cost,
                'interest_percentage' => $supporterInterest,
                'interest_total'      => $amount,
            ];;
            DB::transaction(function () use ($supporter, $amount, $detail, $transaction, $supporterInterest) {
                WalletService::addToWallet($supporter, $amount, $detail);
                $old_d = $transaction->detail;
                $new_d = [Transaction::DETAIL_KEY_INTEREST => [
                    'supporter_id'        => $supporter->id,
                    'total'               => $transaction->total_cost,
                    'interest_percentage' => $supporterInterest,
                    'interest_total'      => $amount,
                ]];
                $transaction->update(['detail' => array_merge($old_d, $new_d)]);
            });
            $user->lastSupporterCalled = "null";
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function getSuccessfulPaymentCount($user)
    {
        return $user->transactions()->where('status', TransactionStatusEnum::SUCCESSFUL)->where('total_cost', '>', '0')->count();
    }
    public function handleReferral($transaction): void
    {
        if ($transaction->status == TransactionStatusEnum::SUCCESSFUL) {
            $user = $transaction->user;
            $invitedUser = InviteFriend::where('user_invited_id', $user->id)->first();
            if ($invitedUser) {
                $referrerUser = $invitedUser->user;
                $percentage = $referrerUser->referralPercentage;

                // Check whether this user should receive a commission from all transactions
                $applyToAllPayment = $referrerUser->apply_to_allPayments;
                if ($applyToAllPayment === false){
                    if ($this->getSuccessfulPaymentCount($user) > 1){
                        return;
                    }
                }

                // If the percentage has been directly specified by the management
                $amount = $transaction->total_cost;
                if (isset($percentage) && $percentage != '' && (int) $percentage > 0) {

                    $percentageReferrer = $amount * (int) $percentage / 100;
                } else {
                    $invitedAndPurchasedUsers = InviteFriend::where('user_id', $referrerUser->id)->where('benefit', '>', '0')->count();
                    if ($invitedAndPurchasedUsers < 6) {
                        $percentage = 5;
                    } elseif ($invitedAndPurchasedUsers < 11) {
                        $percentage = 10;
                    } else {
                        $percentage = 20;
                    }
                    $percentageReferrer = $amount * (int) $percentage / 100;
                }

                //when package with support
                $packageSelected = isset($transaction->detail['package_id']) ? $transaction->detail['package_id'] : null;
                if ($packageSelected) {
                    $package = Package::find($packageSelected);
                    if (!in_array($package->type, [PackageTypeEnum::DIET_AND_EXERCISE_WITH_SUPPORT, PackageTypeEnum::DIET_WITH_SUPPORT])) {
                        $percentageReferrer = max(0, (int) $percentageReferrer);
                    }
                }

                $newBenefit = $invitedUser->benefit + $percentageReferrer;
                $invitedUser->update([
                    'benefit' => $newBenefit,
                ]);
                $detail = ['change_wallet_charge_by' => $user->id, 'transaction_id' => $transaction->id];
                WalletService::addToWallet($referrerUser, $percentageReferrer, $detail);

                // send telegram notification
                $sessions = UserSession::where('user_id', $referrerUser->id)
                    ->where('type', '2')
                    ->where('state', 'waiting_for_agreement')
                    ->first();

                if ($sessions->count()) {
                    $referrerUser->notify(new OrderTelegramMessageNotification(user: $user, benefit: $newBenefit));
                }
            }
        }
    }


    protected function verifySaman(Transaction $transaction, int $amount, array $input = []): array
    {
        $request = request()->all();
        if (isset($request['Status']) && $request['Status'] === '2') {
            return ['success' => true, 'details' => $request, 'reference' => $request['RefNum']];
        }
        return ['success' => false, 'details' => $request, 'reference' => $request['RefNum'] ?? null];
    }

    protected function verifyPaystar(Transaction $transaction, int $amount, array $input = []): array
    {
        try {
            $payStarPin = setting(SettingKeyEnum::PAYMENT_PAYSTAR_TOKEN);
            $payStarSign = setting(SettingKeyEnum::PAYMENT_PAYSTAR_SIGN);

            $refNum = $input['ref_num'] ?? request('ref_num');
            $cardNumber = $input['card_number'] ?? request('card_number');
            $trackingCode = $input['tracking_code'] ?? request('tracking_code');

            $stringToHash = $amount . '#' . $refNum . '#' . $cardNumber . '#' . $trackingCode;
            $sign = hash_hmac('sha512', $stringToHash, $payStarSign);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $payStarPin,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystar.shop/api/pardakht/verify', [
                'ref_num' => $refNum,
                'amount' => $amount,
                'sign' => $sign,
            ]);

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'paystar_api_error', 'details' => $response->body()];
            }

            $json = $response->json();
            if (isset($json['status']) && $json['status'] == 1) {
                return ['success' => true, 'details' => $json, 'reference' => $json['ref_num'] ?? $refNum];
            }

            return ['success' => false, 'message' => $json['message'] ?? 'paystar_verify_failed', 'details' => $json];
        } catch (\Exception $e) {
            Log::error("Paystar verify error: {$e->getMessage()}", ['tx' => $transaction->id]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
