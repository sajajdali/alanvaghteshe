<?php

namespace Modules\Course\Livewire\Admin\Course;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionPaidEnum;
use Modules\Transaction\Enum\TransactionPaymentForEnum;

class CoursePaymentList extends Component
{
    use WithPagination;

    public string $searchPanel = '';

    #[Url]
    public array $search = [];

    public function startSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->where('payment_for', TransactionPaymentForEnum::COURSE)
            ->with(['user'])
            ->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, fn ($query) => $query->where('id', $this->search['id']))
            ->when(isset($this->search['transaction_code']) && filled($this->search['transaction_code']), fn ($query) => $query->where('transaction_code', 'like', '%' . $this->search['transaction_code'] . '%'))
            ->when(isset($this->search['user']) && filled($this->search['user']), fn ($query) => $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $this->search['user'] . '%')->orWhere('mobile', 'like', '%' . $this->search['user'] . '%')))
            ->when(isset($this->search['paid_by']) && filled($this->search['paid_by']), fn ($query) => $query->where('paid_by', $this->search['paid_by']))
            ->latest()
            ->paginate(20);

        return view('course::livewire.admin.course.course-payment-list', [
            'transactions' => $transactions,
            'paidByOptions' => collect(TransactionPaidEnum::cases())
                ->map(fn ($case) => ['value' => $case->value, 'name' => $case->getName()])
                ->all(),
        ])->title('پرداخت‌های دوره');
    }
}
