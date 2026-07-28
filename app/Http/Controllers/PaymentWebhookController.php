<?php
namespace App\Http\Controllers;

use App\Services\PaymentVerifyService;
use Illuminate\Http\Request;
use Modules\Transaction\Entities\Transaction;

class PaymentWebhookController extends Controller
{
    protected $verifyService;

    public function __construct(PaymentVerifyService $verifyService)
    {
        $this->verifyService = $verifyService;
    }

    // POST /payment/{transaction}/callback
    public function handle(Request $request, Transaction $transaction)
    {
        $provider = setting(\Modules\Setting\Enum\SettingKeyEnum::PAYMEN_ACTIVE_DRIVER) ?? 'saman';
        $result = $this->verifyService->verifyAndFinalize($transaction, $provider, $request->all());

        if ($result['success']) {
            return redirect()->route('payment.transaction', [
                'transaction' => $transaction->id,
                'tracking_code' => $transaction->transaction_code,
                'status_payment' => false,
                'call_back' => true
            ]);
        }


        return redirect()->route('payment.transaction', [
            'transaction' => $transaction->id,
            'tracking_code' => $transaction->transaction_code,
            'status_payment' => true,
            'call_back' => true
        ]);
    }
}
