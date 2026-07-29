<?php

namespace Modules\Admin\Livewire;

use App\Notifications\ChatRoomMessageNotification;
use App\Notifications\OrderTelegramMessageNotification;
use App\Services\PaymentVerifyService;
use App\UserSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Api\Http\Controllers\PaymentController;
use Modules\Package\Entities\Package;
use Modules\Package\Enum\PackageTypeEnum;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionStatusEnum;
use Modules\User\app\Models\InviteFriend;
use Modules\User\Entities\User;
use Modules\User\Http\Controllers\WalletController;
use Modules\User\Services\WalletService;
use PharIo\Manifest\InvalidEmailException;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Str;

use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use ZarinPal\Sdk\ClientBuilder;
use ZarinPal\Sdk\Options;
use ZarinPal\Sdk\ZarinPal;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\VerifyRequest;
use ZarinPal\Sdk\HttpClient\Exception\ResponseException;

#[title('پرداخت')]
#[Layout('admin::layouts.login')]
class Payment extends Component
{
    public $transaction = null;
    public $status = 'paying';
    public User $user;

    public $message = '';
    public $recaptcha;

    public string $activePayment = 'saman';

    /**
     * Verify payment and handle post-payment actions.
     */
    private function verifyPayment()
    {
        $amount = $this->transaction->total_cost;
        $driver = $this->activePayment;

        try {
            if ($driver === 'saman') {
                $result = $this->verifySaman($amount);
            } elseif ($driver === 'paystar') {
                $result = $this->verifyPaystar($amount);
            } elseif ($driver === 'zarinpal') {
                $result = $this->verifyZarinpal($amount);

            }

            else {
                $result = $this->verifyPaymentDefault($amount);
            }

            if (!empty($result['success']) && $result['success'] === true) {
                // Merge extra details if any
                $details = $result['details'] ?? [];
                $refId = $result['reference_id'] ?? null;
                $this->processSuccessfulPayment($details, $refId);
                // redirect or render success
                $this->status = 'showMessage';
                $this->message = 'پرداخت با موفقیت انجام و تایید شد';
            } else {
                // Handle driver-specific failure message (if provided)
                $message = $result['message'] ?? ($result['exception']->getMessage() ?? 'خطای نامشخص پرداخت');
                Log::warning("Payment verification failed for driver {$driver}", ['transaction_id' => $this->transaction->id, 'message' => $message]);

                // show message in UI
                $this->status = 'showMessage';
                $this->message = $message;

                // For some failures you may want to redirect to failed route:
                // return redirect()->route('payment.failed')->with('error', $message);
            }
        } catch (InvalidPaymentException $ex) {
            Log::warning('Saman verify failed: '.$ex->getMessage(), ['transaction_id' => $this->transaction->id]);
            return redirect()->route('payment.transaction', [
                'transaction' => $this->transaction->id,
                'tracking_code' => $this->transaction->transaction_code,
                'status_payment' => false,
                'call_back' => true,
            ])->with('error', $ex->getMessage());
        } catch (\Exception $e) {
            Log::error('Payment verification error: '.$e->getMessage(), ['transaction_id' => $this->transaction->id, 'trace' => $e->getTraceAsString()]);
            abort(500, 'خطا در تایید تراکنش: '.$e->getMessage());
        }
    }

    private function verifyZarinpal($amount)
    {
        $clientBuilder = new ClientBuilder();
        $clientBuilder->addPlugin(new HeaderDefaultsPlugin([
            'Accept' => 'application/json',
        ]));

        $options = new Options([
            'client_builder' => $clientBuilder,
            'sandbox' => false,
            'merchant_id' => setting(SettingKeyEnum::PAYMENT_ZARINPAL_MERCHENT),
        ]);

        $zarinpal = new ZarinPal($options);
        $paymentGateway = $zarinpal->paymentGateway();

        $authority = filter_input(INPUT_GET, 'Authority', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_GET, 'Status', FILTER_SANITIZE_STRING);

        if ($status === 'OK') {

            if ($amount) {
                $verifyRequest = new VerifyRequest();
                $verifyRequest->authority = $authority;
                $verifyRequest->amount = $amount;

                try {
                    $response = $paymentGateway->verify($verifyRequest);

                    if ($response->code === 100) {

                        $details = [
                            'reference_id' => $response->ref_id,
                            'card_pan' => $response->card_pan,
                            'fee' => $response->fee,
                        ];
                        return [
                            'success' => true,
                            'details' => $details,
                            'reference_id' => $response->fee,
                        ];
                    } else if ($response->code === 101) {
                        return [
                            'success' => true,
                            'details' => [],
                            'reference_id' => $response->fee,
                        ];
                    } else {
                        return [
                            'success' => false,
                            'details' => [],
                            'message' => "تراکنش نا موفق بود کد : " . $response->code,
                        ];
                    }

                } catch (ResponseException $e) {
                    return [
                        'success' => false,
                        'details' => [],
                        'message' => "تراکنش نا موفق بود دلیل : " .$e->getErrorDetails(),
                    ];
                } catch (\Exception $e) {
                    return [
                        'success' => false,
                        'details' => [],
                        'message' => "خطا در پرداخت دلیل : " .$e->getMessage(),
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'details' => [],
                    'message' => "خطا در پرداخت دلیل : " . 'No Matching Transaction Found For This Authority Code.',
                ];
            }
        } else {
            return [
                'success' => false,
                'details' => [],
                'message' => "خطا در پرداخت دلیل : " . 'Transaction was cancelled or failed.',
            ];
        }
    }

    /**
     * Verify Saman transactions using Shetabit package.
     * Returns array: ['success' => bool, 'details' => array, 'reference_id' => string]
     */
    private function verifySaman(int $amount): array
    {
        try {
            // توجه: Shetabit facade را مطابق پکیج پروژه‌ات import کن
            $receipt = \Shetabit\Payment\Facade\Payment::amount($amount)
                ->transactionId($this->transaction->id)
                ->verify();

            $details = $receipt->getDetails() ?? [];
            $referenceId = $receipt->getReferenceId() ?? null;

            return [
                'success' => true,
                'details' => $details,
                'reference_id' => $referenceId,
            ];
        } catch (InvalidEmailException $ex) {
            // پرداخت ناموفق یا اعتبارسنجی ناموفق
            return [
                'success' => false,
                'exception' => $ex,
                'message' => $ex->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'exception' => $e,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function orderAgain()
    {
        $this->status = 'paying';

    }

    public function verifyPaymentDefault(int $amount)
    {
        try {
            $receipt = \Shetabit\Payment\Facade\Payment::amount($amount)
                ->transactionId($this->transaction->id)
                ->verify();

            $details = $receipt->getDetails() ?? [];
            $referenceId = $receipt->getReferenceId() ?? null;

            return [
                'success' => true,
                'details' => $details,
                'reference_id' => $referenceId,
            ];
        } catch (InvalidEmailException $ex) {
            return [
                'success' => false,
                'exception' => $ex,
                'message' => $ex->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'exception' => $e,
                'message' => $e->getMessage(),
            ];
        }
    }
    /**
     * Verify Paystar transactions via Http request.
     * Expects request()->ref_num, request()->card_number, request()->tracking_code to be present.
     * Returns array: ['success' => bool, 'details' => array, 'message' => string]
     */
    private function verifyPaystar(int $amount): array
    {
        try {
            $payStarPin = setting(SettingKeyEnum::PAYMENT_PAYSTAR_TOKEN);
            $payStarSign = setting(SettingKeyEnum::PAYMENT_PAYSTAR_SIGN);

            if (!$payStarPin || !$payStarSign) {
                return [
                    'success' => false,
                    'message' => 'تنظیمات درگاه PayStar ناقص است',
                ];
            }

            $refNum = request()->ref_num;
            $cardNumber = request()->card_number;
            $trackingCode = request()->tracking_code;

            $stringToHash = $amount . '#' . $refNum . '#' . $cardNumber . '#' . $trackingCode;
            $hashedString = hash_hmac('sha512', $stringToHash, $payStarSign);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $payStarPin,
                'Content-Type' => 'application/json',
            ])->timeout(30)
                ->post('https://api.paystar.shop/api/pardakht/verify', [
                    'ref_num' => $refNum,
                    'amount' => $amount,
                    'sign' => $hashedString,
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'خطا در ارتباط با سرویس پرداخت PayStar',
                    'details' => $response->json(),
                ];
            }

            $json = $response->json();

            if (isset($json['status']) && $json['status'] == 1) {
                // ذخیره نتیجهٔ کامل برای گزارش داخلی
                $detail = $this->transaction->detail;
                $detail['payStar']['payment_result'] = serialize($json);
                $this->transaction->update([
                    'detail' => $detail,
                    'status' => TransactionStatusEnum::SUCCESSFUL,
                ]);

                return [
                    'success' => true,
                    'details' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'پرداخت ناموفق بود',
                'details' => $json,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'exception' => $e,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Common post-success flow: update transaction details/status, assign package, referral and wallet handling.
     *
     * @param array $details Response/details from gateway
     * @param string|null $referenceId Reference/RefId if exists
     */
    private function processSuccessfulPayment(array $details = [], $referenceId = null): void
    {
        // merge into transaction detail in a consistent shape
        $detail = $this->transaction->detail ?? [];

        // keep older fields and add/overwrite important ones
        $merged = array_merge($detail, [
            'gateway_details' => $details,
        ]);

        if ($referenceId) {
            $merged['ref_id'] = $referenceId;
        }

        $this->transaction->update([
            'detail' => $merged,
            'status' => TransactionStatusEnum::SUCCESSFUL,
        ]);

        // post payment actions
        $this->assignPackageToUser();

        // payment event
        app(\App\Services\AppMetricaService::class)->sendEvent(
            profileId: $this->transaction->user->id,
            eventName: 'payment_completed',
            params: [
                'mobile_or_email' => $this->transaction->user->mobile ?? $this->transaction->user->email,
                'name' => $this->transaction->user->full_name,
                'amount' => $this->transaction->total_cost,
                'package_id' => $this->transaction->detail['package_id'] ?? '',
                'diet_plan_id' => $this->transaction->detail['diet_plan_id'] ?? '',
                'transaction_id' => $this->transaction->id,
                'currency' => 'toman',
                'payment_method' => 'online',
            ]
        );

        if ($this->transaction->total_cost > 0) {
            app(PaymentVerifyService::class)->handleReferral($this->transaction);
            app(PaymentVerifyService::class)->handleUserWallet($this->transaction);
        }
    }
    public function handleReferralText()
    {
//        $transaction = Transaction::latest()->first();
        $transaction = Transaction::find(579);
        $this->transaction = $transaction;
        app(PaymentVerifyService::class)->handleReferral($transaction);
    }




    public function mount()
    {
        $this->activePayment = setting(SettingKeyEnum::PAYMEN_ACTIVE_DRIVER);


        if (request()->routeIs('referral.test')) {
            $this->handleReferralText();
        }
        if (request()->query('status_payment')){
            $this->status = 'failed';
        }
        $transaction = request()->route('transaction');
        if (isset($transaction) && !($transaction instanceof Transaction) ) {
            $transaction = Transaction::find($transaction);
        }
        if ($transaction instanceof Transaction) {
            if (request()->tracking_code ) {
                $this->verifyPayment();
            }

            $this->transaction = $transaction;
            $this->user = $this->transaction->user;
            if ($transaction->status == TransactionStatusEnum::PENDING && $transaction->total_cost == 0) {
                $paymentController = new PaymentController();
                $paymentController->assignToUser($this->transaction);
                $this->transaction->update([
                    'status' => TransactionStatusEnum::SUCCESSFUL
                ]);

                $this->status = 'showMessage';
                $this->message = 'پرداخت با استفاده از موجودی شما انجام شد';
            } elseif ($transaction->status == TransactionStatusEnum::SUCCESSFUL) {
                $this->status = 'showMessage';
                $this->message = 'پرداخت شما انجام شده و پکیج برای شما اختصاص یافته است';
            }
        }
    }

    private function gotoPayment()
    {

        // when sandbox
        if (env('PAYMENY_STATUS_SANDBOX') == 'true') {
            $this->transaction->update([
                'status' => TransactionStatusEnum::SUCCESSFUL,
            ]);
            $this->status = 'successful';
            $this->assignPackageToUser();
            return $this->render();
        }

        $amount = $this->transaction->total_cost;
//        if (getRealIp() == '91.92.122.120'){
//            $amount = 10000;
//        }
        $description = 'کاربر پرداخت کننده : ' . $this->user->full_name ?? 'بدون نام' . 'شماره تماس: ' . $this->user->mobile ?? 'بدون موبایل' . 'شماره تراکنش: ' . $this->transaction->id;
//        $invoice = (new Invoice)->amount($amount)
//            ->detail(['description' => $description, 'mobile' => $this->user?->mobile ?? null]);
//        $callbackUrl = route('payment.transaction', ['tracking_code' => $this->transaction->transaction_code, 'transaction' => $this->transaction->id, 'call_back' => true]);
//        $p = \Shetabit\Payment\Facade\Payment::config(['callbackUrl' => $callbackUrl])->purchase(
//            $invoice,
//            function ($driver, $transactionId) {
//                $details = $this->transaction->details;
//                $details['driver'] = $driver;
//                $details['transaction_id_payment'] = $transactionId;
//                $this->transaction->update(['details' => $details]);
//            }
//        )->pay();
//        return $this->redirect($p->getAction());


        $payStarPin = setting(SettingKeyEnum::PAYMENT_PAYSTAR_TOKEN);
        $payStarSign = setting(SettingKeyEnum::PAYMENT_PAYSTAR_SIGN);
        if (!$payStarPin || !$payStarSign) {
            return $this->badRequest('کد درگاه وارد نشده است');
        }
        if ($this->activePayment == 'saman'){
            $callbackUrl = route('payment.callback_saman', ['tracking_code' => $this->transaction->transaction_code, 'transaction' => $this->transaction->id, 'call_back' => true]);
        } else {
            $callbackUrl = route('payment.transaction', ['tracking_code' => $this->transaction->transaction_code, 'transaction' => $this->transaction->id, 'call_back' => true]);
        }

        $orderId = $this->transaction->id;
        if ($this->activePayment == 'saman'){
            $terminalId = setting(SettingKeyEnum::PAYMENT_SAMAN_TERMINAL_NO);
            $resNum = Str::uuid()->toString();
            $response = Http::post('https://sep.shaparak.ir/onlinepg/onlinepg', [
                'action'      => 'token',
                'TerminalId'  => $terminalId,
                'Amount'      => $amount,
                'ResNum'      => $resNum,
                'RedirectUrl' => $callbackUrl,
                'CellNumber'  =>  $this->user->mobile,
            ]);

            $data = $response->json();
            if (!isset($data['status']) || $data['status'] != 1) {
                // خطا در دریافت توکن
                $errorCode = $data['errorCode'] ?? 'unknown';
                $errorDesc = $data['errorDesc'] ?? 'Request failed';
                abort(500, "Error getting token: [$errorCode] $errorDesc");
            }
            $token = $data['token']; // توکن تراکنش

            $u_data = $this->transaction->detail;
            $u_data['detail']['transactionId'] = $token;
            $u_data['detail']['callback'] = $callbackUrl;
            $this->transaction->update([
                'detail' => $u_data,
            ]);

            return redirect()->to(route('payment.saman.form', [
                'token' => $token,
                'callbackUrl' => $callbackUrl
            ]));
        } elseif($this->activePayment == 'paystar') {
            $stringToHash = $amount . '#' . $orderId . '#' . $callbackUrl;
            $hashedString = hash_hmac('sha512', $stringToHash, $payStarSign);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $payStarPin,
                'Content-Type' => 'application/json',
            ])
                ->post('https://api.paystar.shop/api/pardakht/create', [
                    'amount' => $amount,
                    'order_id' => strval($orderId),
                    'callback' => $callbackUrl,
                    'sign' => $hashedString,
                    'callback_method' => 1,
                ]);
            if ($response->json()) {
                if ($response->json()['status'] == 1) {
                    $detail = $this->transaction->detail;
                    $detail['payStar'] = [
                        ['status' => $response->json()['status'] ?? ''],
                        ['token' => $response->json()['token'] ?? ''],
                        ['ref_num' => $response->json()['ref_num'] ?? ''],
                        ['order_id' => $response->json()['order_id'] ?? ''],
                        ['payment_amount' => $response->json()['payment_amount'] ?? ''],
                    ];

                    $this->transaction->update([
                        'detail' => $detail,
                    ]);
                    return redirect()->route('api.payment.redirect_to_bank', [
                        'token' => $response->json()['data']['token'],
                        'amount' => $response->json()['data']['payment_amount'],
                        'ref_num' => $response->json()['data']['ref_num'],
                        'transaction_id' => $this->transaction->id
                    ]);
                } else {
                    $detail['payStar'] = [
                        ['data' => $response->json()['data']],
                    ];
                    $this->transaction->update([
                        'detail' => $detail,
                        'status' => TransactionStatusEnum::REJECTED,
                    ]);
                    dd([
                        'message' => 'خطا در ایجاد تراکنشی',
                        'data' => $response->json('message')
                    ]);
                }
            }
        } else {
            $description = 'کاربر پرداخت کننده : ' . $this->user->full_name ?? 'بدون نام' . 'شماره تماس: ' . $this->user->mobile ?? 'بدون موبایل' . 'شماره تراکنش: ' . $this->transaction->id;
            $invoice = (new Invoice)->amount($amount)
                ->detail(['description' => $description, 'mobile' => $this->user?->mobile ?? null]);
            $p = \Shetabit\Payment\Facade\Payment::config(['callbackUrl' => $callbackUrl])->purchase(
                $invoice,
                function ($driver, $transactionId) {
                    $detail = $this->transaction->detail;
                    $detail['driver'] = $driver;
                    $detail['transaction_id_payment'] = $transactionId;
                    $this->transaction->update(['detail' => $detail]);
                }
            )->pay();
            return $this->redirect($p->getAction());
        }

//        $curlCommand = "curl --location --request POST 'https://api.paystar.shop/api/pardakht/create'" .
//            "--header 'Authorization: Bearer $payStarPin'" .
//            "--header 'Content-Type: application/json'" .
//            "--data-raw '" . json_encode([
//                'amount' => $amount,
//                'order_id' => strval($orderId),
//                'callback' => $callbackUrl,
//                'sign' => $hashedString,
//                'callback_method' => 1,
//            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "'";
//        dd($curlCommand);


    }

    public function onlinePayment()
    {
        if ($this->transaction->status = TransactionStatusEnum::PENDING) {
            $this->gotoPayment();
        }
    }

    private function assignPackageToUser()
    {
        $paymentController = new PaymentController();
        $response = $paymentController->assignToUser($this->transaction);
        if ($response->getStatusCode() == 200) {
            $this->status = 'successful';
        } else {
            $this->status = 'failed';
        }
    }

    public function successfulPayment()
    {
        if ($this->transaction) {
            if ($this->transaction->status == TransactionStatusEnum::PENDING || $this->transaction->status == TransactionStatusEnum::REJECTED) {
                $this->assignPackageToUser();

            } elseif ($this->transaction->status == TransactionStatusEnum::REJECTED) {
                $this->status = 'showMessage';
                $this->message = 'پرداخت شما با مشکل مواجه شده و لطفا مجدد از طریق اپلیکیشن اقدام کنید';

            } elseif ($this->transaction->status == TransactionStatusEnum::SUCCESSFUL) {
                $this->status = 'showMessage';
                $this->message = 'پرداخت شما انجام شده و پکیج برای شما اختصاص یافته است';
            }
        }
    }

    public function paymentAgain()
    {
        $this->status = 'paying';
    }

    public function paymentDollar()
    {
        $detail = $this->transaction->detail ?? [];
        $packageId = $detail['package_id'] ?? null;

        if (! $packageId) {
            $this->status = 'showMessage';
            $this->message = 'برای این نوع پرداخت، لینک پرداخت خارجی تنظیم نشده است.';

            return null;
        }

        $package = Package::find($packageId);
        $productUrl = $package?->detail['api_url'] ?? null;

        if (! $package || ! $productUrl) {
            $this->status = 'showMessage';
            $this->message = 'لینک پرداخت خارجی برای این مورد تنظیم نشده است.';

            return null;
        }

        $userId = $this->user->id;
        $link = "{$productUrl}?user_id={$userId}&transaction_id={$this->transaction->id}";

        return redirect()->away($link);
//        $this->status = 'paypal';
    }

    public function paymentFailed()
    {
        $this->transaction->update(['status' => TransactionStatusEnum::REJECTED]);

        $this->status = 'failed';
    }

    public function render()
    {
        return view('admin::livewire.payment');
    }
}
