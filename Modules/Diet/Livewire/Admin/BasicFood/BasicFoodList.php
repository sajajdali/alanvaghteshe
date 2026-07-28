<?php

namespace Modules\Diet\Livewire\Admin\BasicFood;

use DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Enum\ConditionApplyItemEnum;

class BasicFoodList extends Component
{

    use withPagination;

    #[Url]
    public $search = [];
    public $searchPanel = '';


    #[On('delete')]
    public function delete(BasicFood $model)
    {
        $this->authorize('delete', $model);
        try {

//            DB::table('recipe_basic_foods')->where('basic_food_id', $model->id)->delete();
//            DB::table('basic_food_pivot')->where('basic_food_id', $model->id)->delete();
//            DB::table('basic_food_uni ts')->where('basic_food_id', $model->id)->delete();
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.basic_food.index')->with('success', 'غذای پایه با موفقیت حذف شد.');
    }

    public function startSearch()
    {
        $this->resetPage();
    }



    public function render()
    {
        if (request()->has('search')){
            $this->searchPanel = 'show';
        }
        $basicFoods = BasicFood::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->when(isset($this->search['food_type']) && ! empty($this->search['food_type']), function ($query) {
                return $query->where('type', $this->search['food_type']);
            })
            ->when(isset($this->search['meal']) && filled($this->search['meal']), function ($query) {
                $mealId = (int) $this->search['meal'];

                return $query->whereHas('foods', function ($q) use ($mealId) {
                    $q->whereHas('meals', fn ($mq) => $mq->where('meals.id', $mealId));
                });
            })
            ->when(isset($this->search['recipe']) && ! empty($this->search['recipe']), function ($query) {
                return $query->where('recipe', 'LIKE', "%{$this->search['recipe']}%");
            })
            ->when(isset($this->search['main_nutrition']) && ! empty($this->search['main_nutrition']), function ($query) {
                return $query->where('main_nutrition', $this->search['main_nutrition']);
            })
        ;

        // search conditions
        if (isset($this->search['conditions']) && ! empty($this->search['conditions'])){
            $basicFoods = app('dietService')->handleConditionFoodSearch($basicFoods, $this->search['conditions']);
        }

        $basicFoods = $basicFoods->orderByDesc('id')->paginate(20);

        $conditions = Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::BASIC_FOOD->value)->get();
        $disease    = Disease::active()->get();
        return view('diet::livewire.admin.basic-food.basic-food-list' , compact('basicFoods' , 'conditions' , 'disease'));
    }




}
