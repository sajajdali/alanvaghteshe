<?php

namespace Modules\Exercise\Livewire\Admin\ExerciseBodyCategory;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Exercise\Entities\ExerciseBodyCategory;

#[title('دسته‌بندی اعضای بدن')]
class ExerciseBodyCategoryList extends Component
{
    use withPagination,AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public array $search = [];

    public $searchPanel = '';

    protected $queryString = ['search'];

    #[On('delete')]
    public function delete(ExerciseBodyCategory $model)
    {
        $this->authorize('delete', $model);
        if ($model->has_children) {
            return redirect()->route('admin.exercise_body_category.index')->with('error', 'این دسته دارای زیر دسته است و نمی توانید آن را حذف کنید.');
        }

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.exercise_body_category.index')->with('success', 'دسته بندی با موفقیت حذف شد');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function startSearch()
    {
    }

    public function render()
    {
        $bodyCategories = ExerciseBodyCategory::whereExerciseBodyCategoryId(null)->with('exercises')->get();
        $rowBg = ['bg-primary-transparent', 'bg-danger-transparent', 'bg-success-transparent', 'bg-info-transparent', 'bg-warning-transparent'];

        return view('exercise::livewire.admin.exercise-body-category.exercise-body-category-list', compact('bodyCategories', 'rowBg'));
    }
}
