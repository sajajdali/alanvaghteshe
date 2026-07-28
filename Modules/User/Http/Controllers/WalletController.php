<?php
namespace Modules\User\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\User\Entities\User;
use Modules\User\Services\WalletService;

class WalletController extends Controller
{
    public static function credit(User $user , $amount , $detail = null): \Illuminate\Http\JsonResponse
    {
        WalletService::addToWallet($user, $amount , $detail);
        return response()->json(['message' => 'Wallet credited successfully']);
    }

    public static function debit(User $user ,$amount, $detail = null): \Illuminate\Http\JsonResponse
    {
        try {
            WalletService::subtractFromWallet($user, $amount , $detail);
            return response()->json(['message' => 'Wallet debited successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
