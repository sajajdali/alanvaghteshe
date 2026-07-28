<?php

namespace Modules\Exercise\Livewire\Admin\ExercisePlanStrategy;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Exercise\Entities\ExercisePlanStrategy;

#[title('برنامه ورزشی')]
class ExercisePlanStrategyList extends Component
{
    use withPagination;

    #[Url]
    public $search = [];

    public $searchPanel = '';

    public function createPlan($plan_id)
    {
        $plan = ExercisePlanStrategy::find($plan_id);
        if ($plan) {
            $requestId = $plan->makeRequest();
            if ($requestId) {
                return redirect()->route('admin.exercise_plan_request.edit', $requestId);
            }
        }
        $this->dispatch('error', 'در حال حاضر مشکلی در ثبت درخواست جدید وجود دارد لطفا بعدا تلاش کنید');
    }

    #[On('delete')]
    public function delete(ExercisePlanStrategy $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.exercise_plan_strategy.index')->with('success', 'دسته بندی با موفقیت حذف شد');
    }

    public function startSearch()
    {
        $this->resetPage();
    }

    public function copy(ExercisePlanStrategy $exercisePlanStrategy)
    {
        $this->authorize('update', $exercisePlanStrategy);
        $newPlan = $exercisePlanStrategy->replicate();
        $newPlan->name = $newPlan->name.' - کپی';
        $newPlan->save();
        $newPlan->details()->createMany($exercisePlanStrategy->details->toArray());

        return redirect()->route('admin.exercise_plan_strategy.edit', $newPlan->id);
    }

    public function render()
    {

        $plans = ExercisePlanStrategy::when(isset($this->search['id']) && (int) $this->search['id'] !== 0,
            function ($query) {
                return $query->where('id', $this->search['id']);
            })->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', '%'.$this->search['name'].'%');
            })->when(isset($this->search['gender']) && (int) $this->search['gender'] !== 0, function ($query) {
                return $query->whereGender($this->search['gender']);
            })->when(isset($this->search['level']) && (int) $this->search['level'] !== 0, function ($query) {
                return $query->whereLevel($this->search['level']);
            })->when(isset($this->search['target']) && (int) $this->search['target'] !== 0, function ($query) {
                return $query->whereTarget($this->search['target']);
            })->when(isset($this->search['status']) && ! empty($this->search['status']), function ($query) {
                return $query->whereStatus($this->search['status']);
            })->when(isset($this->search['session']) && (int) $this->search['session'] !== 0, function ($query) {
                return $query->whereSessionCount($this->search['session']);
            })->paginate(15);

        return view('exercise::livewire.admin.exercise-plan-strategy.exercise-plan-strategy-list', compact('plans'));
    }
}
