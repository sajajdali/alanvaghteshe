<?php

namespace Modules\User\Livewire\Admin\User;

use Livewire\Component;
use Modules\Package\Entities\Package;
use Modules\User\Entities\User;

class UserTransactions extends Component
{
    public User $user ;

    public function getPackageName($packageId)
    {
        $package = Package::find($packageId);
        return $package->name;
    }
    public function render()
    {
        $transactions = $this->user->transactions;
        return view('user::livewire.admin.user.user-transactions')->with('transactions', $transactions);
    }
}
