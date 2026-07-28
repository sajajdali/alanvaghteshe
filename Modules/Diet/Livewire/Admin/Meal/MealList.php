<?php

namespace Modules\Diet\Livewire\Admin\Meal;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Diet\Entities\Meal;

#[title('مدیریت وعده ها')]
class MealList extends Component
{

    use withPagination;

    #[Url]
    public $search = [];
    public $searchPanel = '';


    #[On('delete')]
    public function delete(Meal $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.meal.index')->with('success', 'وعده با موفقیت حذف شد.');
    }


    public function render()
    {
        $meals = Meal::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->orderBy('priority')
            ->paginate(20);

        return view('diet::livewire.admin.meal.meal-list', compact('meals'));
    }

}
