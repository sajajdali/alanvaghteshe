<?php

namespace Modules\Diet\Livewire\Admin\FoodUnit;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodUnit;

class FoodUnitList extends Component
{

    use withPagination;

    #[Url]
    public $search = [];
    public $searchPanel = '';


    #[On('delete')]
    public function delete(FoodUnit $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.food_unit.index')->with('success', 'الگو با موفقیت حذف شد.');
    }

    public function startSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $foodUnits = FoodUnit::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->paginate(20);
        return view('diet::livewire.admin.food-unit.food-unit-list', compact('foodUnits'));
    }

}
