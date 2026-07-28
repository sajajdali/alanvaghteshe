<?php

namespace Modules\Admin\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\User\Entities\User;
use Hekmatinasser\Verta\Facades\Verta;
use Modules\User\Services\WalletService;
use Modules\Admin\app\Enums\ActivityEventEnum;

class SupporterActivityHistory extends Component
{
    use WithPagination;
    public ?User $selectedSupporter = null;
    public array $form = [];
    public array $search = [];
    public User $supporter;

    public function modalPayout()
    {
        $this->validate(['form.payout.amount' => 'required'], ['form.payout.amount.required' => 'لطفا مبلغ را وارد کنید.']);
        if ($this->form['payout']['amount'] > $this->supporter->wallet) {
            $this->dispatch('error', message: "مبلغ وارد شده ، از موجودی کاربر بیشتر است!");
            return;
        }
        try {
            WalletService::subtractFromWallet($this->supporter, $this->form['payout']['amount'], null);
            $this->dispatch('success', message: 'تغییرات با موفقیت انجام شد');
            $this->form['payout']['amount'] = null  ;
            $this->dispatch('closeModal',true);
        } catch (\Throwable $th) {
            $this->dispatch('error', message: "خطا در به روز رسانی");
        }
    }
    public function supporterPerformance($user)
    {
        $this->selectedSupporter = User::find($user);
    }
    public function lunchPayoutModal(User $user)
    {
        $this->supporter = $user;
    }
    public function showPerformance()
    {
        $startDate = $endDate = null;
        if (isset($this->form['startDate'])) {
            try {
                $startDate = Verta::parse($this->form['startDate'])->toCarbon();
            } catch (\Throwable $th) {
                $this->dispatch('error', message: "فرمت تاریخ انتخابی صحیح نیست");
                return;
            }
        }
        if (isset($this->form['endDate'])) {
            try {
                $endDate = Verta::parse($this->form['endDate'])->toCarbon();
            } catch (\Throwable $th) {
                $this->dispatch('error', message: "فرمت تاریخ انتخابی صحیح نیست");
                return;
            }
        }
        return  $this->selectedSupporter?->logBy()->when(isset($startDate), function ($q) use ($startDate) {
            $q->where('created_at', '>', $startDate);
        })->when(isset($endDate), function ($q) use ($endDate) {
            $q->where('created_at', '<', $endDate);
        })->when(isset($this->form['condition']), function ($q) {
            $q->where('event', ActivityEventEnum::tryFrom($this->form['condition']));
        });
    }

    public function startLogSearch()
    {
        $this->render();
    }
    public function mount()
    {
        if (!auth()->user()->can('SUPER_ADMIN_ACCESS')) {
            return abort(401);
        }
    }
    public function render()
    {
        $supporters = User::supporters();
        $activityLog = $this->showPerformance()?->paginate(20, pageName: "supporterLog");
        return view('admin::livewire.supporter-activity-history', [
            'supporters' => $supporters->paginate(20, pageName: "supporters"),
            'activityLog' => $activityLog,
        ]);
    }
}
