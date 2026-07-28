<?php
// WalletService.php
namespace Modules\User\Services;
// WalletService.php

use Illuminate\Support\Facades\DB;
use Modules\User\app\Models\WalletTransaction;
use Modules\User\Entities\User;

class WalletService
{
    public static function addToWallet(User $user, int $amount , $detail = null): void
    {
        unset($user->popup);
        DB::transaction(function () use ($user, $amount , $detail) {
            $currentBalance = $user->wallet; // Assuming the User model has a wallet_balance attribute
            $newBalance = $currentBalance + $amount;

            // Create a wallet transaction record
            WalletTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'credit',
                'amount' => $amount,
                'detail' => $detail,
                'balance_after_transaction' => $newBalance,
            ]);

            // Update the user's wallet balance
            $user->update(['wallet' => $newBalance]);
        });
    }

    public static function subtractFromWallet(User $user, int $amount , $detail = null): void
    {
        unset($user->popup);

        DB::transaction(function () use ($user, $amount , $detail) {
            $currentBalance = $user->wallet; // Assuming the User model has a wallet_balance attribute
            $newBalance = $currentBalance - $amount;
            // Ensure the user has enough balance
            if ($newBalance < 0) {
                throw new \Exception('Insufficient balance');
            }

            // Create a wallet transaction record
            WalletTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'debit',
                'amount' => $amount,
                'detail' => $detail,
                'balance_after_transaction' => $newBalance,
            ]);

            // Update the user's wallet balance
            $user->update(['wallet' => $newBalance]);
        });
    }
}
