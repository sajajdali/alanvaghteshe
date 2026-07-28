<?php

namespace Modules\Diet\Livewire\Admin\RequestDiet;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Api\Http\Requests\DietRequest;

#[title('مدیریت درخواست های رژیم')]
class DietListList extends Component
{

    use withPagination;

    #[Url]
    public $search = [];
    public $searchPanel = '';


    #[On('delete')]
    public function delete(DietRequest $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.diet_request.index')->with('success', 'درخواست با موفقیت حذف شد.');
    }

    public function startSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $dietRequests = DietRequest::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
                return $query->where('id', $this->search['id']);
            })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->paginate(20);

        return view('diet::livewire.admin.meal.meal-list', compact('dietRequests'));
    }

}
