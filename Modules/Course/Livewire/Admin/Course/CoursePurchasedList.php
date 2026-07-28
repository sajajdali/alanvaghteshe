<?php

namespace Modules\Course\Livewire\Admin\Course;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Course\app\Models\CourseUser;

class CoursePurchasedList extends Component
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
        $purchases = CourseUser::query()
            ->with(['user', 'course', 'transaction'])
            ->withCount('progress')
            ->with(['course' => fn ($query) => $query->withCount('lessons')])
            ->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, fn ($query) => $query->where('id', $this->search['id']))
            ->when(isset($this->search['course_id']) && (int) $this->search['course_id'] !== 0, fn ($query) => $query->where('course_id', $this->search['course_id']))
            ->when(isset($this->search['course']) && filled($this->search['course']), fn ($query) => $query->whereHas('course', fn ($q) => $q->where('title', 'like', '%' . $this->search['course'] . '%')))
            ->when(isset($this->search['user']) && filled($this->search['user']), fn ($query) => $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $this->search['user'] . '%')->orWhere('mobile', 'like', '%' . $this->search['user'] . '%')))
            ->when(isset($this->search['paid_by']) && filled($this->search['paid_by']), fn ($query) => $query->where('paid_by', $this->search['paid_by']))
            ->when(isset($this->search['is_active']) && $this->search['is_active'] !== '', fn ($query) => $query->where('is_active', (int) $this->search['is_active']))
            ->latest()
            ->paginate(20);

        return view('course::livewire.admin.course.course-purchased-list', [
            'purchases' => $purchases,
            'paidByOptions' => collect(\Modules\Transaction\Enum\TransactionPaidEnum::cases())
                ->map(fn ($case) => ['value' => $case->value, 'name' => $case->getName()])
                ->all(),
        ])
            ->title('دوره‌های خریداری‌شده');
    }
}
