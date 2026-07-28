<?php

namespace Modules\Exercise\Livewire\Admin\ExercisePlanRequest;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Exercise\Entities\ExercisePlanRequest;

#[title('درخواست‌های برنامه')]
class ExercisePlanRequestList extends Component
{
    use withPagination;

    #[Url]
    public $search = [];

    public $searchPanel = '';

    #[On('delete')]
    public function delete(ExercisePlanRequest $model)
    {
        $this->authorize('delete', $model);
        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.exercise_plan_request.index')->with('success', 'درخواست با موفقیت حذف شد');
    }

    public function startSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        $exercisePlanRequests = ExercisePlanRequest::when(auth()->user()?->cannot('exercise_plan_request'), function ($query) {
            $query->whereHas('user', function ($q2) {
                $q2->whereHas('supporter', function ($q3) {
                    $q3->where('support_id', auth()->id());
                });
            });
        })->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })->orderBy(
            'id',
            'desc'
        )->paginate(10);

        return view('exercise::livewire.admin.exercise-plan-request.exercise-plan-request-list', compact('exercisePlanRequests'));
    }
}
