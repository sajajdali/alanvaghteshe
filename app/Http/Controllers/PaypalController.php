<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalService;


namespace App\Http\Controllers;


class PaypalController extends Controller
{
    // دریافت توکن دسترسی از PayPal
    private function getPaypalAccessToken()
    {
        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');

        // ساخت رشته base64 برای احراز هویت
        $credentials = base64_encode("$clientId:$secret");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic $credentials",
            "Content-Type: application/x-www-form-urlencoded"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    // ایجاد پرداخت در PayPal
    private function createPaypalPayment($totalAmount, $currency = "USD")
    {
        $accessToken = $this->getPaypalAccessToken();

        if (!$accessToken) {
            return null;
        }

        $ch = curl_init();

        $data = [
            "intent" => "sale",
            "payer" => [
                "payment_method" => "paypal",
            ],
            "transactions" => [
                [
                    "amount" => [
                        "total" => $totalAmount,
                        "currency" => $currency,
                    ],
                    "description" => "Test payment",
                ]
            ],
            "redirect_urls" => [
                "return_url" => route('payment.success'),
                "cancel_url" => route('payment.cancel'),
            ]
        ];

        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        return $data;
    }

    // تایید پرداخت در PayPal
    private function executePaypalPayment($paymentId, $payerId)
    {
        $accessToken = $this->getPaypalAccessToken();

        if (!$accessToken) {
            return null;
        }

        $ch = curl_init();

        $data = [
            "payer_id" => $payerId
        ];

        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment/$paymentId/execute");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        return $data;
    }

    // ایجاد پرداخت
    public function createPayment()
    {
        $paymentData = $this->createPaypalPayment(10.00); // مبلغ 10 دلار

        if (!$paymentData) {
            return redirect()->route('payment.cancel')->with('status', 'Payment creation failed.');
        }

        // اگر پرداخت موفق بود
        if (isset($paymentData['links'])) {
            foreach ($paymentData['links'] as $link) {
                if ($link['rel'] == 'approval_url') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('payment.cancel')->with('status', 'Payment creation failed.');
    }

    // موفقیت پرداخت
    public function paymentSuccess(Request $request)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');

        $paymentData = $this->executePaypalPayment($paymentId, $payerId);

        if (!$paymentData) {
            return redirect()->route('payment.cancel')->with('status', 'Payment execution failed.');
        }

        if ($paymentData['state'] == 'approved') {
            return redirect()->route('payment.success')->with('status', 'Payment successful!');
        }

        return redirect()->route('payment.cancel')->with('status', 'Payment failed.');
    }

    // لغو پرداخت
    public function paymentCancel()
    {
        return redirect()->route('home')->with('status', 'Payment canceled.');
    }
}
