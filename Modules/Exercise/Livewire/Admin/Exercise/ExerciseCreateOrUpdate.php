<?php

namespace Modules\Exercise\Livewire\Admin\Exercise;

use Livewire\Component;
use Modules\Core\Entities\Disease;
use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Enum\ExerciseConditionEnum;

class ExerciseCreateOrUpdate extends Component
{
    public ?Exercise $exercise = null;

    public string $name;

    public bool $isForMen = false;

    public bool $isForWomen = false;

    public bool $isForBeginner = false;

    public bool $isForAdvanced = false;

    public bool $isForBase = false;

    public bool $isForComplementary = false;

    public bool $isMain = true;

    public bool $isBeforeAfter = false;

    public string $type = 'main';

    public array $category = [];

    public array $superExercises = [];

    public $isEdited = false;

    public $videoUrl;

    public array $diseasesExercise = [];

    protected $rules = [
        'name' => 'required|string',
    ];

    protected $messages = [
        'name.required' => 'نام ورزش را وارد کنید',
    ];

    public function mount()
    {
        $exercise = request()->route('exercise');
        if ($exercise instanceof Exercise) {
            $this->authorize('update', $exercise);
            $this->isEdited = true;
            $this->exercise = $exercise;
            $this->name = $this->exercise->name ?? '';
            $this->isForMen = $this->exercise->is_for_men ?? false;
            $this->isForWomen = $this->exercise->is_for_women ?? false;
            $this->isForBeginner = $this->exercise->is_for_beginner ?? false;
            $this->isForAdvanced = $this->exercise->is_for_advanced ?? false;
            $this->isForBase = $this->exercise->is_base ?? false;
            $this->isForComplementary = $this->exercise->is_complementary ?? false;
            $this->category = $this->exercise->bodyCategories?->pluck('id')->toArray() ?? [];
            $this->videoUrl = $this->exercise->video ?? '';
            $this->superExercises = $this->exercise->superCanNotExercises?->pluck('id')->toArray() ?? [];
            $this->diseasesExercise = $this->exercise->canNotDiseases?->pluck('id')->toArray() ?? [];
            $this->isMain = $this->exercise->is_main ?? true;
            $this->isBeforeAfter = $this->exercise->is_before_after ?? false;

            if ($this->isBeforeAfter) {
                $this->type = 'before_after';
            }
        }
    }

    public function updatedType($value): void
    {
        if ($value == 'main') {
            $this->isMain = true;
            $this->isBeforeAfter = false;
        } else {
            $this->isMain = false;
            $this->isBeforeAfter = true;
        }
    }

    public function updateOrCreate()
    {
        $this->validate();
        $condition = ExerciseConditionEnum::make(
            isForMen: $this->isForMen,
            isForWomen: $this->isForWomen,
            isForBeginners: $this->isForBeginner,
            isForAdvanced: $this->isForAdvanced,
            isExerciseBased: $this->isForBase,
            isExerciseComplementary: $this->isForComplementary,
            isMainExercise: $this->isMain,
            isBeforeAfterExercise: $this->isBeforeAfter
        );
        $message = 'ورزش با موفقیت اضافه شد';
        if ($this->isEdited) {
            $this->exercise->update([
                'name' => $this->name,
                'video' => $this->videoUrl,
                'conditions' => $condition,
            ]);
            $message = 'ورزش با موفقیت ویرایش شد';
        } else {
            $this->exercise = Exercise::create([
                'name' => $this->name,
                'video' => $this->videoUrl,
                'conditions' => $condition,
            ]);
        }

        $this->exercise->bodyCategories()->sync($this->category);
        $this->exercise->superCanNotExercises()->syncWithPivotValues($this->superExercises, ['type' => 'can_not']);
        $this->exercise->canNotDiseases()->syncWithPivotValues($this->diseasesExercise, ['type' => 'can_not']);

        return redirect()->route('admin.exercise.index')->with('success', $message);
    }

    public function render()
    {
        $title = (! is_null($this->exercise) && $this->exercise->exists) ? 'ویرایش ورزش' : 'ایجاد ورزش جدید';
        $parentCategories = ExerciseBodyCategory::where('exercise_body_category_id', null)->get();
        $superableExercises = Exercise::where('id', '!=', $this->exercise->id ?? 0)->get();
        $diseases = Disease::active()->priority()->get();

        return view('exercise::livewire.admin.exercise.exercise-create-or-update', compact('parentCategories', 'superableExercises', 'diseases'))->title($title);
    }
}
