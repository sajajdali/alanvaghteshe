<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;
use Modules\Api\Http\Controllers\PaymentController;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionStatusEnum;

class AdminController extends Controller
{
    public function logout()
    {
        \Illuminate\Support\Facades\Auth::guard('web')->logout();

        session()->flush(); // Clear session data

        return redirect(url('/'));
    }

    public function gumroadPing(Request $request)
    {
        $data = $request->all();

        Log::info('📦 Gumroad Ping Received', $data);

        // اعتبارسنجی Seller ID
        if ($request->get('seller_id') !== env('GUMROAD_SELLER_ID')) {
            return response()->json(['error' => 'Invalid seller ID'], 403);
        }
        // successfully transaction
        $custom = $request->input('custom_fields');
        $transactionId = $custom['transaction_id'] ?? null;
//        $userId = $custom['user_id'] ?? null;

        $transaction = Transaction::find($transactionId);
        if ($transaction->status == TransactionStatusEnum::PENDING || $transaction->status == TransactionStatusEnum::REJECTED) {
            $paymentController = new PaymentController();
            $paymentController->assignToUser($transaction);
        }

        return response()->json(['status' => 'ok']);
    }
}
