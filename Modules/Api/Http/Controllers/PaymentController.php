<?php

namespace Modules\Api\Http\Controllers;

use App\Services\Payments\PaymentPricingService;
use App\Services\Payments\PaymentSubjectResolver;
use App\Services\Payments\TransactionFinalizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Modules\Api\Enum\PopupEnum;
use Modules\Api\Http\Requests\CourseDiscountRequest;
use Modules\Api\Http\Requests\CourseStoreRequest;
use Modules\Api\Http\Requests\DiscountRequest;
use Modules\Api\Http\Requests\StoreRequest;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\Package\PackageResource;
use Modules\Api\Transformers\UserResource;
use Modules\Coupon\Entities\Coupon;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum;
use Modules\Package\Entities\Package;
use Modules\Package\Enum\PackageTypeEnum;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\Reminder\app\Models\Reminder;
use Modules\Reminder\app\Models\ReminderQueue;
use Modules\Reminder\Enum\ReminderParametersEnum;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionPaidEnum;
use Modules\Transaction\Enum\TransactionStatusEnum;
use Modules\User\Enum\UserMetaEnum;
use Modules\User\Http\Controllers\WalletController;

class PaymentController extends Controller
{
    use ApiHandlerTrait;

    public function discount(DiscountRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->discountForPackage($request);
    }

    public function courseDiscount(CourseDiscountRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->discountForCourse($request);
    }


    public function bazar(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->bazarForPackage($request);
    }

    public function courseBazar(CourseStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->bazarForCourse($request);
    }

    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->storeForPackage($request);
    }

    public function courseStore(CourseStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->storeForCourse($request);
    }

    private function discountForPackage(DiscountRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subject = app(PaymentSubjectResolver::class)->resolvePackageRequest($request);
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->respondDiscount($request, $subject);
    }

    private function discountForCourse(CourseDiscountRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subject = app(PaymentSubjectResolver::class)->resolveCourseRequest($request, $request->user());
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->respondDiscount($request, $subject);
    }

    private function bazarForPackage(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subject = app(PaymentSubjectResolver::class)->resolvePackageRequest($request);
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->createBazarTransaction($request, $subject);
    }

    private function bazarForCourse(CourseStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subject = app(PaymentSubjectResolver::class)->resolveCourseRequest($request, $request->user());
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->createBazarTransaction($request, $subject);
    }

    private function storeForPackage(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subject = app(PaymentSubjectResolver::class)->resolvePackageRequest($request);
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->createOnlineTransaction($request, $subject);
    }

    private function storeForCourse(CourseStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subject = app(PaymentSubjectResolver::class)->resolveCourseRequest($request, $request->user());
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->createOnlineTransaction($request, $subject);
    }

    private function respondDiscount(Request $request, $subject): \Illuminate\Http\JsonResponse
    {
        $coupon = Coupon::query()
            ->active()
            ->where('code', $request->input('discount_code'))
            ->first();

        $result = app(PaymentPricingService::class)->calculateWithCoupon($coupon, $subject, $request->user());

        if (! $result['status']) {
            return $this->badRequest($result);
        }

        return $this->ok(array_merge(
            $result,
            $subject->toDiscountResponse($result['amount'])
        ));
    }

    private function createBazarTransaction(Request $request, $subject): \Illuminate\Http\JsonResponse
    {
        $unique_id = $request->input('unique_id');
        $user = $request->user();

        $exists = Transaction::where('detail->unique_id', $unique_id)->exists();

        if ($exists) {
            return $this->badRequest([
                'status' => false,
                'message' => 'این تراکنش قبلاً استفاده شده است',
            ]);
        }

        try {
            $subject = app(PaymentSubjectResolver::class)->resolve($request, $user);
        } catch (\DomainException $exception) {
            return $this->badRequest([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        $pricing = $this->resolvePricing($request, $subject);
        if (! $pricing['status']) {
            return $this->badRequest($pricing);
        }

        $newTransaction = Transaction::create([
            'user_id' => $user->id,
            'coupon_id' => $pricing['coupon_id'] ?? null,
            'transaction_code' => Transaction::generateTransactionCode(),
            'status' => TransactionStatusEnum::SUCCESSFUL,
            'cost' => $subject->originalPrice,
            'payment_for' => $subject->paymentFor->value,
            'total_cost' => $pricing['amount'],
            'discount' => $subject->baseDiscount + $pricing['discount'],
            'ip' => request()->ip(),
            'paid_by' => TransactionPaidEnum::BY_BAZAR,
            'detail' => array_merge($subject->detail, [
                'unique_id' => $unique_id,
                'user_wallet' => $pricing['required_amount_of_wallet'],
            ]),
        ]);

        // payment event
        app(\App\Services\AppMetricaService::class)->sendEvent(
            profileId: $user->id,
            eventName: 'payment_completed',
            params: [
                'mobile_or_email' => $user->mobile ?? $user->email,
                'name' => $user->full_name,
                'amount' => $pricing['amount'],
                'package_id' => $subject->detail['package_id'] ?? '',
                'diet_plan_id' => $subject->detail['diet_plan_id'] ?? '',
                'course_id' => $subject->detail['course_id'] ?? '',
                'transaction_id' => $newTransaction->id,
                'currency' => 'toman',
                'payment_method' => 'bazar',
            ]
        );

        app(TransactionFinalizer::class)->complete($newTransaction);

        return $this->ok([
            'status' => true,
            'message' => 'پرداخت شما توسط کافه بازار با موفقیت انجام شد',
        ]);
    }

    private function createOnlineTransaction(Request $request, $subject): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $pricing = $this->resolvePricing($request, $subject);
        if (! $pricing['status']) {
            return $this->badRequest($pricing);
        }

        $newTransaction = Transaction::create([
            'user_id' => $user->id,
            'coupon_id' => $pricing['coupon_id'] ?? null,
            'transaction_code' => Transaction::generateTransactionCode(),
            'status' => TransactionStatusEnum::PENDING,
            'cost' => $subject->originalPrice,
            'payment_for' => $subject->paymentFor->value,
            'total_cost' => $pricing['amount'],
            'discount' => $subject->baseDiscount + $pricing['discount'],
            'ip' => request()->ip(),
            'paid_by' => TransactionPaidEnum::ONLINE,
            'detail' => array_merge($subject->detail, [
                'user_wallet' => $pricing['required_amount_of_wallet'],
            ]),
        ]);

        //if payment in sanbox mood

        // when No need to pay the amount
        if ($pricing['amount'] == 0) {
//            $this->assignToUser($newTransaction);
//            $this->handleWallet($newTransaction);
            return $this->ok([
                'status' => true,
                'message' => 'پرداخت با استفاده از موجودی کیف پول انجام خواهد شد',
                'payment_link' => str_replace("api", 'crm', route('payment.transaction', $newTransaction)),
            ]);
        }
        // when No need to pay the amount

        return $this->ok([
            'status' => true,
            'payment_link' => str_replace("api", 'crm', route('payment.transaction', $newTransaction)),
        ]);
    }

    private function resolvePricing(Request $request, $subject): array
    {
        $pricingService = app(PaymentPricingService::class);

        if ($request->filled('discount_code') && $request->filled('coupon_id')) {
            $coupon = Coupon::query()
                ->active()
                ->whereKey($request->input('coupon_id'))
                ->where('code', $request->input('discount_code'))
                ->first();

            return $pricingService->calculateWithCoupon($coupon, $subject, $request->user());
        }

        return $pricingService->calculateWithoutCoupon($subject, $request->user());
    }

    private function insertReminders($userModel, $package)
    {
        ReminderQueue::where('user_id', $userModel->id)
            ->where('reminder_for', ReminderTypeEnum::PACKAGE_PURCHASED)
            ->delete();
        $package_reminder = Reminder::where(function ($query) {
            $query->where('send_for', ReminderTypeEnum::PACKAGE_PURCHASED)
                ->orWhere('send_for', ReminderTypeEnum::PACKAGE_EXPIRED_NO_RENEWAL);
        })->get();

        if ($package_reminder->isNotEmpty()) {
            $package_reminder->each(function ($remidner) use ($package, $userModel) {
                $detail = [];
                $params = $remidner->parameters;
                if (!empty($params)) {
                    foreach ($params as $param) {
                        $detail['params'][] = ReminderParametersEnum::tryFrom($param);
                    }
                }

                if ($remidner->send_for == ReminderTypeEnum::PACKAGE_EXPIRED_NO_RENEWAL) {
                    $send_at = \now()->addDays($package->days)->addDays($remidner->send_day)->addHours($remidner->send_time);
                } else {
                    $send_at = \now()->addDays($remidner->send_day)->addHours($remidner->send_time);
                }
                if ($remidner->send_day < 0 && $remidner->send_for == ReminderTypeEnum::PACKAGE_PURCHASED) {
                    $send_at = \now()->addDays($package->days)->subDays(abs($remidner->send_day));
                }
                $model = [
                    'user_id' => $userModel->id,
                    'reminder_type' => $remidner->status,
                    'reminder_for' => $remidner->send_for,
                    'reminder_id' => $remidner->id,
                    'send_at' => $send_at,
                    'detail' => $detail
                ];
                ReminderQueue::create($model);
            });
        }
    }

    public function assignToUser($transaction)
    {
        app(TransactionFinalizer::class)->complete($transaction);

        return $this->ok(
            [
                'status' => true,
                'user' => UserResource::make($transaction->user)
            ]
        );
    }

    public function packages(Request $request)
    {
        $user = $request->user();
        $dietType = $user->diet_type;
        $support = $request->get('with_support') ?? '1';
        if ($dietType == '') {
            return $this->badRequest('اطلاعات کاربر ناقص است');
        }
        if ($support == '1') {
            $packages = Package::active()->dietWithSupport()->orderByDesc('priority')->orderByDesc('id')->get();
        } else {
            $packages = Package::active()->diet()->orderByDesc('priority')->orderByDesc('id')->get();

        }
        if (!$packages) {
            return $this->badRequest('هیچ بسته ای برای شما یافت نشد. لطفا با پشتیبانی تماس بگیرید');
        }
        return $this->ok([
            'status' => true,
            'wallet' => $user->wallet,
            'expire_at' => 10,
            'package' => PackageResource::collection($packages)
        ]);
    }

    public function assginExersicePlan($user)
    {
        if ($user->how_much_experience_sports == 1 || $user->how_much_experience_sports == 0) {
            $user_level = 1;
        } else {
            $user_level = 2;
        }

        $exerse_forUser = ExercisePlanStrategy::where('gender', ExercisePlanStrategyGenderEnum::map((int)$user->gender))
            ->where('target', ExercisePlanStrategyTargetEnum::map((int)$user->target_of_exercise))
            ->where('level', ExercisePlanStrategyLevelEnum::map((int)$user_level))
            ->where('session_count', ((int)$user->how_many_days_week_exercise + 1))?->get();
        if ($exerse_forUser->isNotEmpty()) {
            //previous user plan
            $user_previous_plans = ExercisePlanRequest::where('user_id', $user->id)->pluck('exercise_plan_strategy_id');
            $hasNewPlan = Arr::flatten(array_diff($exerse_forUser->pluck('id')->toArray(), $user_previous_plans->toArray()));
            if (count($hasNewPlan) > 0) {
                $user_can_get = $hasNewPlan[0];
            } else {
                $user_can_get = ExercisePlanRequest::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->whereNot('status', ExercisePlanRequestEnum::FAILED)?->first()?->id;
            }
        }
        //check if any plan exist for assigning to user
        if (!empty($user_can_get)) {
            $plan = ExercisePlanStrategy::find($user_can_get);
            //assign plan
            $chishod = $plan->makeRequest($user->id);
        }
    }

    public function assignDietPlan($user)
    {

        app('dietService')->computeCalorie($user);
        $conditions = app('dietService')->getUserConditions($user);
        $dietPlans = app('dietService')->getDietPlans($conditions);
        $dietPlan = app('dietService')->insertUserDietPlan($user, $dietPlans->first());
        if ($dietPlan['status']) {
            app('dietService')->makeRejim($dietPlan['dietRequestModel'], DietRequest::ACTION_REQUEST_INSERT);
        }
    }

    public function redirectToBank(Request $request)
    {
        $data = collect([
            'token' => $request->input('token'),
            'amount' => $request->input('amount'),
            'ref_num' => $request->input('ref_num'),
        ]);
        return view('api::redirect_to_bank', compact('data'));
    }

    public function verify(Request $request)
    {
        $transaction = Transaction::find($request->input('transaction_id'));
        $amount = $transaction->total_cost;
        if ($request->tracking_code && $request->card_number && $request->status) {
            $payStarPin = setting(SettingKeyEnum::PAYMENT_PAYSTAR_TOKEN);
            $payStarSign = setting(SettingKeyEnum::PAYMENT_PAYSTAR_SIGN);
            $stringToHash = $amount . '#' . $request->ref_num . '#' . $request->card_number . '#' . $request->tracking_code;
            $hashedString = hash_hmac('sha512', $stringToHash, $payStarSign);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $payStarPin,
                'Content-Type' => 'application/json',
            ])->timeout(30)
                ->post('https://core.paystar.ir/api/pardakht/verify', [
                    'ref_num' => $request->ref_num,
                    'amount' => $amount,
                    'sign' => $hashedString,
                ]);
            if ($response->json()) {

                if ($response->json()['status'] == 1) {
                    $detail = $transaction->detail;
                    $detail['payStar']['payment_result'] = serialize($response->json());
                    $transaction->update([
                        'detail' => $detail,
                        'status' => TransactionStatusEnum::SUCCESSFUL,
                    ]);
                } else {
                    $paymentStatus = false;
                    $message = $response->json()['message'];
                    return view('api::verify', compact('transaction', 'paymentStatus', 'message'));
                }
            }
        } else {
            $paymentStatus = false;
            $message = 'خطا در ایجاد تراکنش - لطفا مجدد اقدام کنید';
            return view('api::verify', compact('transaction', 'paymentStatus', 'message'));
        }
        return view('api::verify');
    }

    public function handleWallet(Transaction $transaction): void
    {
        app(TransactionFinalizer::class)->complete($transaction);
    }

    private function insertDietRequest($user)
    {
        $user->dietRequests()->create(
            [
                'diet_plan_id' => ''
            ]
        );
    }
}
