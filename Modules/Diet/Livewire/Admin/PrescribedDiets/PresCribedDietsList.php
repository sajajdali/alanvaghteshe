<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets;

use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;

class PresCribedDietsList extends Component
{
    use WithPagination;

    public $searchPanel = '';

    #[Url]
    public $search = [];
    public $dateSearch = [];


    public function startSearch()
    {
        $this->resetPage();
        if (!empty($this->dateSearch['requestDate'])) {
            $this->search['requestDate'] = $this->dateSearch['requestDate'];
        }
        if (!empty($this->dateSearch['sendDate'])) {
            $this->search['sendDate'] = $this->dateSearch['sendDate'];
        }
        if (!empty($this->dateSearch['endDate'])) {
            $this->search['endDate'] = $this->dateSearch['endDate'];
        }
    }

    public function resetProperties()
    {
        $this->search = [];
        $this->dateSearch = [];
        $this->resetPage();
    }

    #[On('delete')]
    public function deleteDiet($id)
    {
        DietRequest::findOrFail($id)->delete();
        session()->flash('success', 'رژیم با موفقیت حذف شد.');
        $this->resetPage();
    }

    public function render()
    {
        // dd($this->search['requestDate']);
        $diet = DietRequest::query()
            ->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
                return $query->where('id', 'LIKE', '%' . $this->search['id'] . '%');
            })->when(isset($this->search['name']), function ($query) {
                return $query->where('diet_plan_id', 'LIKE', (DietPlan::where('id', 'LIKE', '%' . $this->search['name'] . '%')->first()?->id) ?? 0);
            })->when(isset($this->search['requestDate']), function ($query) {
                return $query->whereDate('created_at', Verta::parse($this->search['requestDate'])->toCarbon()->toDateString());
            })->when(isset($this->search['sendDate']), function ($query) {
                return $query->whereDate('updated_at', Verta::parse($this->search['sendDate'])->toCarbon()->toDateString());
            })->when(isset($this->search['endDate']), function ($query) {
                return $query->whereDate('end_date', Verta::parse($this->search['endDate'])->toCarbon()->toDateString());
            })
            ->orderByDesc('id')
            ->paginate(20);

        return view('diet::livewire.admin.prescribed-diets.pres-cribed-diets-list', [
            'diets' => $diet
        ]);
    }
}
