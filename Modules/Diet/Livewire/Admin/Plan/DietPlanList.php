<?php

namespace Modules\Diet\Livewire\Admin\Plan;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Enum\ConditionApplyItemEnum;

class DietPlanList extends Component
{
    use withPagination;

    #[Url]
    public $search = [];

    public $searchPanel = '';

    #[On('delete')]
    public function delete(DietPlan $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.diet_plan.index')->with('success', 'پلن رژیم با موفقیت حذف شد');
    }

    public function startSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $dietPlans = DietPlan::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->when(isset($this->search['status']) && ! empty($this->search['status']), function ($query) {
                return $query->where('status', "{$this->search['status']}");
            })

            ->when(isset($this->search['day_count']) && ! empty($this->search['day_count']), function ($query) {
                return $query->where('day_count', "{$this->search['day_count']}");
            })
        ;
        // search conditions
        if (isset($this->search['conditions']) && ! empty($this->search['conditions'])){
            $dietPlans = app('dietService')->handleConditionFoodSearch($dietPlans, $this->search['conditions']);
        }
        $dietPlans = $dietPlans->paginate(20);

        $conditions = Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::FOOD->value)->get();
        $disease    = Disease::active()->get();
        return view('diet::livewire.admin.plan.diet-plan-list', compact('dietPlans' , 'conditions' , 'disease'));
    }
}
