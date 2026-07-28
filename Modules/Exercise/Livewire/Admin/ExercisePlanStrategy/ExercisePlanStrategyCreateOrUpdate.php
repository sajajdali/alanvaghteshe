<?php

namespace Modules\Exercise\Livewire\Admin\ExercisePlanStrategy;

use Livewire\Component;
use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\Exercise\Enum\ExercisePlanStrategyDetailTypeEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum;
use Modules\Exercise\Enum\ExerciseSetTypeEnum;

class ExercisePlanStrategyCreateOrUpdate extends Component
{
    public ?ExercisePlanStrategy $planStrategy = null;

    public bool $isEdited = false;

    public int $step = 1;

    public string $name = '';

    public string $session_count = '1';

    public int $gender = ExercisePlanStrategyGenderEnum::MALE->value;

    public int $target = ExercisePlanStrategyTargetEnum::TARGET_INCREASE_WEIGHT->value;

    public int $level = ExercisePlanStrategyLevelEnum::BEGINNER->value;

    public bool $status = true;

    public $parentCategories;

    public $beforeAfterExercises;

    public array $planDetails = [];

    public array $beforeAfter = [];

    public array $dayCopy = [];

    public array $dayCopyTo = [];

    public function mount()
    {
        $exercise_plan_strategy = request()->route('exercise_plan_strategy');
        if ($exercise_plan_strategy instanceof ExercisePlanStrategy) {
            $this->authorize('update', $exercise_plan_strategy);
            $this->isEdited = true;
            $this->planStrategy = $exercise_plan_strategy;
            $this->name = $exercise_plan_strategy->name;
            $this->session_count = $exercise_plan_strategy->session_count;
            $this->gender = $exercise_plan_strategy->gender->value;
            $this->target = $exercise_plan_strategy->target->value;
            $this->level = $exercise_plan_strategy->level->value;
            $this->status = $exercise_plan_strategy->status;
            //get plan details
            for ($i = 0; $i < $exercise_plan_strategy->session_count; $i++) {
                //check is copy stategy
                if ($i > 0 && $exercise_plan_strategy->details()->where('day', $i)->copy()->exists()) {
                    $this->dayCopy[] = $i;
                    $this->dayCopyTo[$i] = $exercise_plan_strategy->details()->where('day', $i)->copy()->first()->exerciseable_id;
                    $this->planDetails[$i] = [];
                    continue;
                }
                //before exercise
                $beforeExercise = $exercise_plan_strategy->details()->where('day', $i)->before()->first();
                if ($beforeExercise) {
                    $this->beforeAfter[$i]['exercise_before_id'] = $beforeExercise->exerciseable_id;
                    $this->beforeAfter[$i]['exercise_before_reps'] = $beforeExercise->reps[0] ?? '';
                }
                //details item
                $planDetails = $exercise_plan_strategy->details()->where('day', $i)->main()->orderBy('id')->get();
                if ($planDetails->isNotEmpty()) {
                    foreach ($planDetails as $plan_item) {
                        $hasSuper = $plan_item->has_super_set;
                        $superSet = null;
                        if ($hasSuper) {
                            $superSetItem = $plan_item->superSet;
                            if ($superSetItem) {
                                $superSet = [
                                    'exercise_category_id' => $superSetItem->exerciseable_id,
                                    'reps' => $superSetItem->reps,
                                    'type' => $superSetItem->type->is(ExercisePlanStrategyDetailTypeEnum::BASE) ? '1' : '2',
                                ];
                            }
                        }
                        $this->planDetails[$i][] = [
                            'exercise_category_id' => $plan_item->exerciseable_id,
                            'reps' => $plan_item->reps,
                            'type' => $plan_item->type->is(ExercisePlanStrategyDetailTypeEnum::BASE) ? '1' : '2',
                            'has_super' => $hasSuper,
                            'super_set' => $superSet,
                        ];
                    }
                }
                //after exercise
                $afterExercise = $exercise_plan_strategy->details()->where('day', $i)->after()->first();
                if ($afterExercise) {
                    $this->beforeAfter[$i]['exercise_after_id'] = $afterExercise->exerciseable_id;
                    $this->beforeAfter[$i]['exercise_after_reps'] = $afterExercise->reps[0] ?? '';
                }
            }
        }
    }

    public function updatedDayCopy($value): void
    {
        $this->dayCopyTo[$value] ??= 0;
        $this->dispatch('updateUi');
    }

    public function backToStart(): void
    {
        $this->step = 1;
    }

    public function goToNextStep(): void
    {
        //validate step 1
        $this->validate([
            'name' => 'required|string',
            'session_count' => 'required|numeric',
        ], [
            'name.required' => 'نام برنامه را وارد کنید',
            'session_count.required' => 'تعداد جلسات برنامه را وارد کنید',
            'session_count.numeric' => 'تعداد جلسات برنامه باید عدد باشد',
        ]);
        //make planDetails array equal to session_count
        if (count($this->planDetails) != $this->session_count) {
            if (count($this->planDetails) > $this->session_count) {
                //remove items until length of planDetails be equal to session_count
                $this->planDetails = array_slice($this->planDetails, 0, $this->session_count);
            } else {
                //add items until length of planDetails be equal to session_count
                for ($i = count($this->planDetails); $i < $this->session_count; $i++) {
                    $this->planDetails[$i] = [];
                    $this->beforeAfter[$i]['exercise_before_id'] = '';
                    $this->beforeAfter[$i]['exercise_after_id'] = '';
                    $this->beforeAfter[$i]['exercise_after_reps'] = '';
                    $this->beforeAfter[$i]['exercise_before_reps'] = '';
                }
            }
        }
        $this->dispatch('updateUi');
        //got to next step
        $this->step = 2;
    }

    public function addPlanDetail($day): void
    {
        //add empty array to planDetails
        $this->planDetails[$day][] = [
            'exercise_category_id' => $this->parentCategories->first()->id,
            'reps' => [
                0,
                0,
                0,
                0,
                0,
            ],
            'type' => 'base',
            'has_super' => false,
        ];
        $this->dispatch('updateUi');
    }

    public function updatedPlanDetails($value, $index): void
    {
        if (str_contains($index, 'has_super') && (bool) $value) {
            //get day and itemInDay
            $indexItems = explode('.', $index);
            $day = $indexItems[0];
            $indexInDay = $indexItems[1];
            if (! isset($this->planDetails[$day][$indexInDay]['super_set']['exercise_category_id'])) {
                $this->planDetails[$day][$indexInDay]['super_set']['exercise_category_id'] = $this->parentCategories->first()->id;
            }
            if (! isset($this->planDetails[$day][$indexInDay]['super_set']['base'])) {
                $this->planDetails[$day][$indexInDay]['super_set']['type'] = 'base';
            }
            if (! isset($this->planDetails[$day][$indexInDay]['super_set']['reps'])) {
                $this->planDetails[$day][$indexInDay]['super_set']['reps'] = [
                    0,
                    0,
                    0,
                    0,
                    0,
                ];
            }
            $this->dispatch('updateUi');
        }
    }

    public function setExerciseCategory($cat_id, $day, $index): void
    {
        $this->planDetails[$day][$index]['exercise_category_id'] = $cat_id;
    }

    public function setExerciseCategorySuperSet($cat_id, $day, $index): void
    {
        $this->planDetails[$day][$index]['super_set']['exercise_category_id'] = $cat_id;
    }

    public function setBeforeExercise($ext_id, $day): void
    {
        $this->beforeAfter[$day]['exercise_before_id'] = $ext_id;
    }

    public function setCopyTo($data, $day): void
    {
        $this->dayCopyTo[$day] = $data;
    }

    public function setAfterExercise($ext_id, $day): void
    {
        $this->beforeAfter[$day]['exercise_after_id'] = $ext_id;
    }

    public function removePlanDetail($day, $itemInDay): void
    {
        //remove item from planDetails
        unset($this->planDetails[$day][$itemInDay]);
        //resort planDetails
        $this->planDetails[$day] = array_values($this->planDetails[$day]);
    }

    public function render()
    {
        $bgHeadersClasses = ['bg-primary', 'bg-info', 'bg-success', 'bg-danger', 'bg-warning', 'bg-secondary'];
        $title = (! is_null($this->planStrategy) && $this->planStrategy->exists) ? 'ویرایش برنامه' : 'ایجاد برنامه جدید';
        //get main categories
        $this->parentCategories = ExerciseBodyCategory::main()->get();
        //get before after exercises
        $this->beforeAfterExercises = Exercise::secondary()->get();

        return view('exercise::livewire.admin.exercise-plan-strategy.exercise-plan-strategy-create-or-update',
            compact('bgHeadersClasses'))->title($title);
    }

    public function submit()
    {
        //validate data
        //check at least one exercise for each day
        for ($i = 0; $i < $this->session_count; $i++) {
            if (\in_array($i, $this->dayCopy)) {
                continue;
            }
            if (count($this->planDetails[$i]) == 0) {
                $this->dispatch('error', message: 'برای روز '.($i + 1).' برنامه ای وارد نشده است');

                return;
            }
        }
        //check at least one reps sets and super sets should be set
        for ($i = 0; $i < $this->session_count; $i++) {
            if (\in_array($i, $this->dayCopy)) {
                continue;
            }
            $jMax = is_array($this->planDetails[$i]) ? count($this->planDetails[$i]) : 0;
            for ($j = 0; $j < $jMax; $j++) {
                $reps = array_filter($this->planDetails[$i][$j]['reps'], function ($item) {
                    return $item > 0;
                });
                if (count($reps) == 0) {
                    $this->dispatch('error', message: 'برای روز '.($i + 1).' حرکت '.($j + 1).' تعداد تکرار ها وارد نشده است');

                    return;
                }
                if (isset($this->planDetails[$i][$j]['has_super']) && $this->planDetails[$i][$j]['has_super']) {
                    $reps = array_filter($this->planDetails[$i][$j]['super_set']['reps'], function ($item) {
                        return $item > 0;
                    });
                    if (count($reps) == 0) {
                        $this->dispatch('error', message: 'برای روز '.($i + 1).' سوپر ست '.($j + 1).' تعداد تکرار ها وارد نشده است');

                        return;
                    }
                }
            }
        }
        $message = 'پلان تمرینی با موفقیت اضافه شد';
        //save data
        if ($this->isEdited) {
            $message = 'پلان تمرینی با موفقیت ویرایش شد';
            //update strategy
            $this->planStrategy->update([
                'name' => $this->name,
                'session_count' => $this->session_count,
                'target' => ExercisePlanStrategyTargetEnum::tryFrom($this->target) ?? ExercisePlanStrategyTargetEnum::getDefault(),
                'gender' => ExercisePlanStrategyGenderEnum::tryFrom($this->gender) ?? ExercisePlanStrategyGenderEnum::getDefault(),
                'level' => ExercisePlanStrategyLevelEnum::tryFrom($this->level) ?? ExercisePlanStrategyLevelEnum::getDefault(),
                'status' => $this->status,
            ]);
            //remove all old details
            $this->planStrategy->details()->delete();
        } else {
            //create a new strategy
            $this->planStrategy = ExercisePlanStrategy::create([
                'name' => $this->name,
                'session_count' => $this->session_count,
                'target' => ExercisePlanStrategyTargetEnum::tryFrom($this->target) ?? ExercisePlanStrategyTargetEnum::getDefault(),
                'gender' => ExercisePlanStrategyGenderEnum::tryFrom($this->gender) ?? ExercisePlanStrategyGenderEnum::getDefault(),
                'level' => ExercisePlanStrategyLevelEnum::tryFrom($this->level) ?? ExercisePlanStrategyLevelEnum::getDefault(),
                'status' => $this->status,
            ]);
        }
        //save details from planDetails
        for ($i = 0; $i < $this->session_count; $i++) {
            //check is copy plan
            if ($i > 0 && \in_array($i, $this->dayCopy)) {
                $this->planStrategy->details()->create([
                    'exerciseable_type' => ExercisePlanStrategy::class,
                    'exerciseable_id' => $this->dayCopyTo[$i] ?? 0,
                    'super_set_id' => null,
                    'type' => ExercisePlanStrategyDetailTypeEnum::COPY,
                    'set_type' => ExerciseSetTypeEnum::COPY_SET,
                    'day' => $i,
                    'reps' => [0, 0, 0, 0, 0],
                ]);

                continue;
            }
            //store before exercises
            if (isset($this->beforeAfter[$i]['exercise_before_id']) && (int) $this->beforeAfter[$i]['exercise_before_id'] != 0 && Exercise::find($this->beforeAfter[$i]['exercise_before_id']) != null) {
                $this->planStrategy->details()->create([
                    'exerciseable_type' => Exercise::class,
                    'exerciseable_id' => $this->beforeAfter[$i]['exercise_before_id'],
                    'super_set_id' => null,
                    'type' => ExercisePlanStrategyDetailTypeEnum::BEFORE,
                    'set_type' => ExerciseSetTypeEnum::BEFORE_SET,
                    'day' => $i,
                    'reps' => \Arr::wrap($this->beforeAfter[$i]['exercise_before_reps']),
                ]);
            }
            //store main plan
            foreach ($this->planDetails[$i] as $item) {
                //check if item has super set
                $superSetId = null;
                if (isset($item['has_super']) && (bool) $item['has_super']) {
                    //insert super-set
                    $superSetId = $this->planStrategy->details()->create([
                        'exerciseable_type' => ExerciseBodyCategory::class,
                        'exerciseable_id' => $item['super_set']['exercise_category_id'] ?? $this->parentCategories->first()->id,
                        'super_set_id' => null,
                        'set_type' => ExerciseSetTypeEnum::SUPER_SET,
                        'type' => ExercisePlanStrategyDetailTypeEnum::tryFrom((int) $item['super_set']['type']) ?? ExercisePlanStrategyDetailTypeEnum::getDefault(),
                        'day' => $i,
                        'reps' => $item['super_set']['reps'] ?? [0, 0, 0, 0, 0],
                    ]);
                }
                //insert main set
                $this->planStrategy->details()->create([
                    'exerciseable_type' => ExerciseBodyCategory::class,
                    'exerciseable_id' => $item['exercise_category_id'] ?? $this->parentCategories->first()->id,
                    'super_set_id' => $superSetId?->id ?? null,
                    'set_type' => ExerciseSetTypeEnum::MAIN_SET,
                    'type' => ExercisePlanStrategyDetailTypeEnum::tryFrom((int) $item['type']) ?? ExercisePlanStrategyDetailTypeEnum::getDefault(),
                    'day' => $i,
                    'reps' => $item['reps'] ?? [0, 0, 0, 0, 0],
                ]);
            }
            //store after exercises
            if (isset($this->beforeAfter[$i]['exercise_after_id']) && (int) $this->beforeAfter[$i]['exercise_after_id'] != 0 && Exercise::find($this->beforeAfter[$i]['exercise_after_id']) != null) {
                $this->planStrategy->details()->create([
                    'exerciseable_type' => Exercise::class,
                    'exerciseable_id' => $this->beforeAfter[$i]['exercise_after_id'],
                    'super_set_id' => null,
                    'type' => ExercisePlanStrategyDetailTypeEnum::AFTER,
                    'set_type' => ExerciseSetTypeEnum::AFTER_SET,
                    'day' => $i,
                    'reps' => \Arr::wrap($this->beforeAfter[$i]['exercise_after_reps']),
                ]);
            }
        }

        return redirect()->route('admin.exercise_plan_strategy.index')->with('success', $message);
    }
}
