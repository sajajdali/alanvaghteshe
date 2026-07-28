<?php

namespace Modules\Admin\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\Title;
use Modules\User\Entities\User;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionStatusEnum;

#[title('پیشخوان مدیریت')]
class Dashboard extends Component
{
    //transactions prop
    public $transactionsCostToday, $transactionPrecent,$transactions;

    //diet Prop
    public $todaydiets, $endedDiet;

    //user Prop
    public $registeredUsers;

    //chart Transactions prop
    public array $chartData;
    public $transactiosnYearlyCost;
    protected function transactionCaculation()
    {
        $this->transactionsCostToday = Transaction::whereDate('created_at', Carbon::now()->today())
            ->where('status', TransactionStatusEnum::SUCCESSFUL)?->sum('total_cost') ?? 0;
        $transactionsCostyesterday = Transaction::whereDate('created_at', Carbon::now()->yesterday())
            ->where('status', TransactionStatusEnum::SUCCESSFUL)?->sum('total_cost') ?? 0;
        if ($transactionsCostyesterday != 0 &&  $this->transactionsCostToday != 0) {
            $transactionProgress = (($this->transactionsCostToday / $transactionsCostyesterday) -  $this->transactionsCostToday);
            if ($transactionProgress > 0) {
                $this->transactionPrecent = ['amount' => ($transactionProgress * 100), 'status' => '+'];
            } else {
                $this->transactionPrecent =  ['amount' => (($this->transactionPrecent - 1) * 100), 'status' => '-'];
            }
        } else {
            $this->transactionPrecent =  ['amount' => 0, 'status' => ''];
        }

        $this->transactiosnYearlyCost  =  Transaction::whereBetween('created_at', [Carbon::now(), Carbon::now()->subYear()])
            ->where('status', TransactionStatusEnum::SUCCESSFUL)?->sum('total_cost') ?? 0;

            $this->transactions = Transaction::where([
                ['status', TransactionStatusEnum::SUCCESSFUL],
                ['total_cost' , '>' , '0']
            ])->orderByDesc('id')?->get();
    }
    protected function dietPlan()
    {
        $this->todaydiets = DietRequest::where('status', DietRequestStatusEnum::ACTIVE)
            ->whereDate('updated_at', Carbon::now()
                ->today())?->count() ?? 0;
        $this->endedDiet = DietRequest::where('status', DietRequestStatusEnum::END)
            ->whereDate('updated_at', Carbon::now()
                ->today())?->count() ?? 0;
    }

    protected function UserRegisteredNumber()
    {
        $this->registeredUsers = User::whereBetween('created_at', [Verta::now()->subWeek(), Verta::now()])?->count();
    }
    protected function chartData()
    {
        $transactionGroup = Transaction::where('status', TransactionStatusEnum::SUCCESSFUL)
            ->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()])
            ->get()
            ->groupBy(function ($date) {
                return Verta::parse($date->created_at)->format('%B'); // Group by month
            });

        $this->chartData = [];

        foreach ($transactionGroup as $month => $groupedTransactions) {
            $this->chartData['mount'][] = $month; // Month name
            $this->chartData['amount'][] = $groupedTransactions->sum('total_cost'); // Sum of the 'cost' column
        }
    }
    public function mount()
    {
        $this->transactionCaculation();
        $this->dietPlan();
        $this->UserRegisteredNumber();
        $this->chartData();
    }
    public function render()
    {
        if (auth()->user()->isDefaultUSer()) {
            $inviteFriends = auth()->user()->inviteFriends;
            $referralStatics = [
                'number_invited_friends' => $inviteFriends->count(),
                'total_benefit' => number_format($inviteFriends->sum('benefit')),
                'successful_referrals' => number_format($inviteFriends->where('benefit', '>', '0')->count()),
            ];
            return view('admin::livewire.dashboard_referral' , compact('referralStatics'))->layout('admin::layouts.app_referral');

        } else {
            return view('admin::livewire.dashboard');
        }
    }
}
