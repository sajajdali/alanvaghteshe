<?php

namespace Modules\Diet\Livewire\Admin\NutritionTrip;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Diet\app\Models\NutritionTip;

#[title('مدیریت نکات تغذیه')]
class NutritionTipList extends Component
{
    use withPagination;

    #[Url]
    public $search = [];
    public $searchPanel = '';

    #[On('delete')]
    public function delete(NutritionTip $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.nutrition_tip.index')->with('success', 'نکته تغذیه با موفقیت حذف شد.');
    }

    public function render()
    {
        $nutritionTips = NutritionTip::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->when(isset($this->search['description']) && ! empty($this->search['description']), function ($query) {
                return $query->where('description', 'LIKE', "%{$this->search['description']}%");
            })
            ->orderByDesc('id')
            ->paginate(20);
        return view('diet::livewire.admin.nutrition-trip.nutrition-tip-list' , compact('nutritionTips'));
    }
}
