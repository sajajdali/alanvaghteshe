<?php

namespace Modules\Exercise\Livewire\Admin\ExerciseBodyCategory;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Exercise\Entities\ExerciseBodyCategory;

class ExerciseBodyCategoryUpdateOrCreate extends Component
{
    use AuthorizesRequests;
    public ?ExerciseBodyCategory $category = null;

    public string $name = '';
    public ?string $parent_id = null;

    public $isEdited = false;

    protected $rules = [
        'name' => 'required|string'
    ];

    protected $messages = [
        'name.required' => 'نام دسته بندی را وارد کنید',
    ];

    public function mount()
    {
        $exercise_body_category = request()->route('exercise_body_category');
        if ($exercise_body_category instanceof ExerciseBodyCategory) {
            $this->authorize('update', $exercise_body_category);
            $this->isEdited = true;
            $this->category = $exercise_body_category;
            $this->name = $this->category->name ?? '';
            $this->parent_id = $this->category->exercise_body_category_id ?? null;
        }
    }

    public function updateOrCreate()
    {
        $this->validate();
        $message = 'دسته بندی با موفقیت ایجاد شد';
        if($this->isEdited) {
            $this->category->update([
                'name' => $this->name,
                'exercise_body_category_id' => empty($this->parent_id) ? null : $this->parent_id
            ]);
            $message = 'دسته بندی با موفقیت ویرایش شد';
        }else{
            ExerciseBodyCategory::create([
                'name' => $this->name,
                'exercise_body_category_id' => empty($this->parent_id) ? null : $this->parent_id
            ]);
        }
        return redirect()->route('admin.exercise_body_category.index')->with('success',$message);
    }

    public function render()
    {
        $title = (! is_null($this->category) && $this->category->exists) ? 'ویرایش دسته' : 'ایجاد دسته جدید';
        $parentCategories = ExerciseBodyCategory::when($this->isEdited, function ($query) {
            $query->where('id', '!=', $this->category->id);
        })->get()->pluck('name', 'id')->toArray();
        return view('exercise::livewire.admin.exercise-body-category.exercise-body-category-update-or-create',compact('parentCategories'))->title($title);
    }
}
