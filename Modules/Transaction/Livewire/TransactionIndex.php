<?php

namespace Modules\Transaction\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Hekmatinasser\Verta\Facades\Verta;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionPaymentForEnum;

class TransactionIndex extends Component
{

    use WithPagination;
    public array $search = [];

    public function startSearch()
    {
        $this->render();
    }
    public function resetSearch()
    {
        unset($this->search);
    }
    public function searchIn()
    {
        $startDate = Carbon::now()->addDay();
        if (isset($this->search['startDate'])) {
            try {
                $startDate = Verta::parse($this->search['startDate'])->toCarbon();
            } catch (\Throwable $th) {
                $this->dispatch('error', message: 'فرمت تاریخ شروع صحیح نیست');
            }
        }
        $endDate = Carbon::now();
        if (isset($this->search['endDate'])) {
            try {
                $endDate = Verta::parse($this->search['endDate'])->toCarbon();
            } catch (\Throwable $th) {
                $this->dispatch('error', message: 'فرمت تاریخ پایان صحیح نیست');
            }
        }
        return Transaction::query()
            ->with('user')
            ->latest()
            ->when(isset($this->search['payment_for']) && filled($this->search['payment_for']), function ($q) {
                $q->where('payment_for', $this->search['payment_for']);
            })
            ->when(isset($this->search['condition']), function ($q) use ($startDate, $endDate) {
            switch ($this->search['condition']) {
                case "1":
                    $q->successful()->whereHas('user', function ($qq) use ($startDate, $endDate) {
                        $qq->whereHas('packages', function ($qqq) use ($startDate, $endDate) {
                            $qqq->where('type', PackageUserTypeEnum::IN_USE)
                                ->where('start_at', '<', $startDate->copy()->toDateString())
                                ->where('end_at', '>', $endDate->copy()->toDateString());
                        });
                    });
                    break;

                case "2":
                    $q->successful()->whereHas('user', function ($qq) use ($startDate, $endDate) {
                        $qq->whereHas('packages', function ($qqq) use ($startDate, $endDate) {
                            $qqq->where('type', PackageUserTypeEnum::IN_USE)
                                ->where('end_at', '>', $endDate->copy()->addDays(5));
                        });
                    });
                    break;

                case "3":
                    $q->whereHas('user', function ($qq) {
                        $qq->whereDoesntHave('packages', function ($qqq) {
                            $qqq->where('type', PackageUserTypeEnum::IN_USE);
                        })->whereHas('packages', function ($qqq) {
                            $qqq->where('type', PackageUserTypeEnum::END);
                        });
                    });
                    break;

                case "4":
                    $q->successful()->whereHas('user', function ($qq) use ($startDate, $endDate) {
                        $qq->whereHas('packages', function ($qqq) use ($startDate, $endDate) {
                            $qqq->where('type', PackageUserTypeEnum::IN_USE)
                                ->where('start_at', '>', $startDate->copy()->addDays(15));
                        });
                    });
                    break;
            }
        });
    }
    public function render()
    {
        $t = $this->searchIn();
        return view('transaction::livewire.transaction-index', [
            'transactions' =>  $t->paginate(50),
            'paymentForOptions' => collect(TransactionPaymentForEnum::cases())
                ->map(fn ($case) => ['value' => $case->value, 'name' => $case->getName()])
                ->all(),
        ]);
    }
}
