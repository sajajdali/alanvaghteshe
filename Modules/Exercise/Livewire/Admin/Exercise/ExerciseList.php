<?php

namespace Modules\Exercise\Livewire\Admin\Exercise;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Entities\Disease;
use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Enum\ExerciseConditionEnum;

#[title('ورزش‌ها')]
class ExerciseList extends Component
{
    use withPagination, AuthorizesRequests;

    #[Url]
    public array $search = [];

    public $searchPanel = '';

    #[On('delete')]
    public function delete(Exercise $model)
    {
        $this->authorize('delete', $model);
        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.exercise.index')->with('success', 'ورزش با موفقیت حذف شد');
    }

    public function startSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $diseases = Disease::active()->priority()->get();
        $bodyCategories = ExerciseBodyCategory::select('id', 'name')->get();
        return view('exercise::livewire.admin.exercise.exercise-list', [
            'exercises' => Exercise::when(isset($this->search['id']) && (int) $this->search['id'] !== 0,
                function ($query) {
                    return $query->where('id', $this->search['id']);
                })->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                    return $query->where('name', 'LIKE', '%'.$this->search['name'].'%');
                })->when(isset($this->search['gender']) && ! empty($this->search['gender']), function ($query) {
                    if ($this->search['gender'] == 1) {
                        $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_MEN->value, true);
                    } else {
                        $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_WOMEN->value, true);
                    }
                })->when(isset($this->search['type']) && ! empty($this->search['type']), function ($query) {
                    if ($this->search['type'] == 1) {
                        $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_EXERCISE_BASED->value, true);
                    } else {
                        $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_EXERCISE_COMPLEMENTARY->value,
                            true);
                    }
                })->when(isset($this->search['level']) && ! empty($this->search['level']), function ($query) {
                    if ($this->search['level'] == 1) {
                        $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_BEGINNERS->value, true);
                    } else {
                        $query->whereJsonContains('conditions->'.ExerciseConditionEnum::IS_FOR_ADVANCED->value, true);
                    }
                })
                ->when(isset($this->search['category']) && ! empty($this->search['category']), function ($query) {
                    return $query->whereHas('bodyCategories', function ($query) {
                        $query->where('exercise_body_categories.id', $this->search['category']);
                    });
                })->when(isset($this->search['diseases']) && (int) $this->search['diseases'] !== 0,function ($query){
                    return $query->whereHas('canNotDiseases', function ($query) {
                        $query->where('diseases.id', $this->search['diseases']);
                    });
                })
                ->with('bodyCategories')->paginate(20),
            'bodyCategories' => $bodyCategories,
            'diseases' => $diseases,
        ]);
    }
}
