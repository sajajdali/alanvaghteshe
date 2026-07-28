<?php

namespace Modules\Exercise\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Queue\SerializesModels;
use Modules\Exercise\Entities\Exercise;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Setting\Enum\SettingKeyEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Exercise\Enum\ExerciseSetTypeEnum;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\Exercise\Entities\ExercisePlanRequestDetail;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyDetailTypeEnum;

class MakeExercisePlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $exercisePlanRequestId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //all item of plan
        $planItems = [];
        $usedExerciseId = [];
        //find request
        $requestPlan = ExercisePlanRequest::find($this->exercisePlanRequestId);
        //check request exists
        if (!$requestPlan->exists) {
            return;
        }
        //find strategy according the target, gender, level
        $strategy = $requestPlan->exercisePlanStrategy;
        $planForUser = $requestPlan->user;
        //return error if strategy not exists
        if (!$strategy) {
            $this->error(sprintf('%s هیچ برنامه تمرینی برای جنسیت %s با سطح %s و هدف %s یافت نشد', 'خطای 210: ',
                $requestPlan->gender->getName(), $requestPlan->level->getName(), $requestPlan->target->getName()));

            return;
        }
        //start arrange plan items
        $strategyDetails = $strategy->details;
        //check strategy details is not empty
        if ($strategyDetails->isEmpty()) {
            $this->error(sprintf('%s برنامه تمرینی برای الگو %s خالی است', 'خطای 220: ', $strategy->name));

            return;
        }

        for ($i = 0, $iMax = $strategy->session_count; $i < $iMax; $i++) {
            $dayPlan = $strategyDetails->where('day', $i);

            if ($dayPlan->isEmpty()) {
                $this->error(sprintf('%s در برنامه تمرینی %s برای روز %s هیج برنامه‌ای تعریف نشده است.', 'خطای 230: ',
                    $strategy->name,
                    $i + 1));

                return;
            }

            //check is there any copy exercise
            if ($i > 0 && $dayPlan->where('type', ExercisePlanStrategyDetailTypeEnum::COPY)->isNotEmpty()) {
                $copy = $dayPlan->where('type', ExercisePlanStrategyDetailTypeEnum::COPY)->first();
                $copyDay = $copy->exerciseable_id;
                if (isset($planItems[$copyDay])) {
                    //copy all exercise from day $copyDay to day $i
                    foreach ($planItems[$copyDay] as $item) {
                        $planItems[$i][] = [
                            'exercise_id' => $item['exercise_id'],
                            'exercise_name' => $item['exercise_name'],
                            'type' => $item['type'],
                            'set_type' => $item['set_type'],
                            'day' => $i,
                            'exercise_body_category_id' => $item['exercise_body_category_id'],
                            'reps' => $item['reps'],
                            'has_super_set' => $item['has_super_set'] ?? false,
                            'super_set' => $item['super_set'] ?? null
                        ];
                    }
                    continue;
                }
            }

            //get before exercise
            $before = $dayPlan->where('type', ExercisePlanStrategyDetailTypeEnum::BEFORE);
            if ($before->isNotEmpty()) {
                /** @var Exercise $exerciseBefore */
                $exerciseBefore = $before->first()->exerciseable()->first();
                if ($exerciseBefore) {
                    $planItems[$i][] = [
                        'exercise_id' => $exerciseBefore->id,
                        'exercise_name' => $exerciseBefore->name,
                        'type' => ExercisePlanStrategyDetailTypeEnum::BEFORE,
                        'set_type' => ExerciseSetTypeEnum::BEFORE_SET,
                        'day' => $i,
                        'exercise_body_category_id' => null,
                        'reps' => $before->first()->reps,
                        'has_super_set' => false,
                    ];
                }
            }//end before

            $dayMainExercises = $dayPlan->where('set_type', ExerciseSetTypeEnum::MAIN_SET)->sortBy('id');
            if ($dayMainExercises->isEmpty()) {
                $this->error(sprintf('%s در برنامه تمرین اصلی %s برای روز %s تعریف نشده است.', 'خطای 310: ',
                    $strategy->name, $i + 1));

                return;
            }

            foreach ($dayMainExercises as $mainExerciseStrategy) {
                //super set exercise
                $superSet = [];
                //check exercise has super set
                $hasSuperSet = $mainExerciseStrategy->has_super_set;
                //is any superset in this category
                //find exercise from strategy
                $mainExercise = $this->getExerciseFromStrategy(
                    category: $mainExerciseStrategy->exerciseable,
                    gender: $strategy->gender,
                    level: $strategy->level,
                    type: $mainExerciseStrategy->type,
                    userDiseases: $planForUser?->diseases->pluck('id')->toArray(),
                    hasSuperSet: $hasSuperSet,
                    usedExerciseId: $usedExerciseId
                );
                //return error if there is no exercise
                if (!($mainExercise instanceof Exercise)) {
                    $this->error($mainExercise);

                    return;
                }
                if ($hasSuperSet) {//get super set exercise
                    /** @var ExerciseBodyCategory $categorySuperSet */
                    $categorySuperSet = $mainExerciseStrategy->superSet->exerciseable;

                    $superSetExercise = $this->getSuperSet(
                        category: $categorySuperSet,
                        exercise: $mainExercise,
                        gender: $strategy->gender,
                        level: $strategy->level,
                        type: $mainExerciseStrategy->superSet->type,
                        usedExerciseId: $usedExerciseId,
                        userDiseases: $planForUser?->diseases->pluck('id')->toArray());
                    if (!($superSetExercise instanceof Exercise)) {
                        $this->error($superSetExercise);

                        return;
                    }
                    $usedExerciseId[] = $superSetExercise->id;
                    $superSet = [
                        'exercise_id' => $superSetExercise->id,
                        'exercise_name' => $superSetExercise->name,
                        'type' => $mainExerciseStrategy->superSet->type ?? ExercisePlanStrategyDetailTypeEnum::getDefault(),
                        'exercise_body_category_id' => $categorySuperSet->id,
                        'day' => $i,
                        'reps' => $mainExerciseStrategy->superSet->reps ?? [0, 0, 0, 0, 0],
                        'has_super_set' => false,
                    ];
                }
                //add main exercise to plan items
                $usedExerciseId[] = $mainExercise->id;
                $planItems[$i][] = [
                    'exercise_id' => $mainExercise->id,
                    'exercise_name' => $mainExercise->name,
                    'type' => $mainExerciseStrategy->type,
                    'set_type' => ExerciseSetTypeEnum::MAIN_SET,
                    'exercise_body_category_id' => $mainExerciseStrategy->exerciseable_id,
                    'day' => $i,
                    'reps' => $mainExerciseStrategy->reps,
                    'has_super_set' => $hasSuperSet,
                    'super_set' => $superSet,
                ];
            }

            //get after exercise
            $after = $dayPlan->where('type', ExercisePlanStrategyDetailTypeEnum::AFTER);
            if ($after->isNotEmpty()) {
                /** @var Exercise $exerciseAfter */
                $exerciseAfter = $after->first()->exerciseable()->first();
                if ($exerciseAfter) {
                    $planItems[$i][] = [
                        'exercise_id' => $exerciseAfter->id,
                        'exercise_name' => $exerciseAfter->name,
                        'exercise_body_category_id' => null,
                        'type' => ExercisePlanStrategyDetailTypeEnum::AFTER,
                        'set_type' => ExerciseSetTypeEnum::AFTER_SET,
                        'day' => $i,
                        'reps' => $after->first()->reps,
                        'has_super_set' => false,
                    ];
                }
            }//end after
        }//end day $i

        //insert all data to database
        for ($i = 0; $i < $iMax; $i++) {
            foreach ($planItems[$i] as $item) {
                $superSetId = null;
                if ($item['has_super_set']) {
                    $superSetId = ExercisePlanRequestDetail::create([
                        'exercise_plan_request_id' => $requestPlan->id,
                        'exercise_id' => $item['super_set']['exercise_id'],
                        'exercise_name' => $item['super_set']['exercise_name'],
                        'exercise_body_category_id' => $item['super_set']['exercise_body_category_id'],
                        'set_type' => ExerciseSetTypeEnum::SUPER_SET,
                        'type' => $item['super_set']['type'],
                        'day' => $item['super_set']['day'],
                        'reps' => array_filter($item['super_set']['reps'], function ($rep) {
                            //check rep is not zero or string
                            return $rep != '0';
                        }),
                    ]);
                }

                $repsArray = $item['reps'];
                if (count($repsArray) === 5) {
                    $repsArray = array_filter($repsArray, function ($rep) {
                        return $rep != '0';
                    });
                }
                ExercisePlanRequestDetail::create([
                    'exercise_plan_request_id' => $requestPlan->id,
                    'exercise_id' => $item['exercise_id'],
                    'exercise_name' => $item['exercise_name'],
                    'exercise_body_category_id' => $item['exercise_body_category_id'],
                    'type' => $item['type'],
                    'day' => $item['day'],
                    'reps' => $repsArray,
                    'set_type' => $item['set_type'] ?? ExerciseSetTypeEnum::MAIN_SET,
                    'super_set_id' => $superSetId?->id ?? null,
                ]);
            }
        }

        //get status of request
        $status = ExercisePlanRequestEnum::tryFrom(setting(SettingKeyEnum::DEFAULT_EXERCISE_STATUS)) ?? ExercisePlanRequestEnum::PENDING;
        //update request status to completed
        $requestPlan->update([
            'status' => $status,
            'start_at' => now(),
            'end_at' => now()->addWeeks(4),
        ]);
        $this->createPdfFile($requestPlan);
        $this->complete();
    }
    private function createPdfFile(ExercisePlanRequest $exercise_plan_request)
    {
        //create pdf
        $details = $exercise_plan_request->details()->orderBy('id')->get();
        $mainFile =  View::make('exercise::admin.pdf', compact('exercise_plan_request','details'));
        $fileName = 'exercise-'. $exercise_plan_request->id . '.html';
        $file_path = '/tmp/pdf/' . $fileName;
        File::put($file_path, $mainFile);
        File::chmod($file_path, 0777);

    }
    private function error(?string $message): void
    {
        $requestPlan = ExercisePlanRequest::find($this->exercisePlanRequestId);
        $requestPlan?->update([
            'status' => ExercisePlanRequestEnum::FAILED,
            'message' => $message,
        ]);
        $this->complete();
    }

    private function complete(): void
    {
        //send event
        event(new \Modules\Exercise\Events\ExercisePlanRequestCompletedEvent($this->exercisePlanRequestId));
    }

    private function getExerciseFromStrategy(
        ExerciseBodyCategory $category,
        ExercisePlanStrategyGenderEnum $gender,
        ExercisePlanStrategyLevelEnum $level,
        ExercisePlanStrategyDetailTypeEnum $type,
        ?array $userDiseases,
        bool $hasSuperSet = false,
        array $usedExerciseId = [],
    ): Exercise|string {
        $possibleExercises = $category->exercises()->main()->gender($gender)->level($level)->type($type)
            ->when(is_array($userDiseases) && count($userDiseases), function ($query) use ($userDiseases) {
                return $query->whereDoesntHave('canNotDiseases', function ($query) use ($userDiseases) {
                    return $query->whereIn('diseases.id', $userDiseases);
                });
            })->whereNotIn('id', $usedExerciseId)->get();
        //if found at least one exercise return random exercise
        if ($possibleExercises->isNotEmpty()) {
            return $possibleExercises->random();
        }
        //error array
        $error = [
            $category->exercises()->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)
                ->when(is_array($userDiseases) && count($userDiseases), function ($query) use ($userDiseases) {
                    return $query->whereDoesntHave('canNotDiseases', function ($query) use ($userDiseases) {
                        return $query->whereIn('diseases.id', $userDiseases);
                    });
                })->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)
                ->when(is_array($userDiseases) && count($userDiseases), function ($query) use ($userDiseases) {
                    return $query->whereDoesntHave('canNotDiseases', function ($query) use ($userDiseases) {
                        return $query->whereIn('diseases.id', $userDiseases);
                    });
                })->whereNotIn('id', $usedExerciseId)->exists(),
        ];
        $messages = [
            sprintf('%s در دسته « %s » تمرینی تعریف نشده است.', 'خطای 500: ', $category->name),
            sprintf('%s در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » تعریف نشده است.', 'خطای 510: ',
                $category->name, $type->getName(), $gender->getName(), $level->getName()),
            sprintf('%s در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » که ممنوعیت استفاده بیماری نداشته باشد، تعریف نشده است.',
                'خطای 520: ',
                $category->name, $type->getName(), $gender->getName(), $level->getName()),
            sprintf('%s تعداد تمرین تعریف شده در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » که ممنوعیت استفاده بیماری نداشته باشد، کافی نیست.',
                'خطای 530: ',
                $category->name, $type->getName(), $gender->getName(), $level->getName()),
        ];
        //handle error base on exception occur
        if (!$category->exercises()->main()->gender($gender)->level($level)->type($type)->exists()) {
            return sprintf('%s در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » یافت نشد', 'خطای ۱۴۰: ',
                $category->name, $type->getName(), $gender->getName(), $level->getName());
        }

        //return error message first false in error array
        foreach ($error as $key => $item) {
            if (!$item) {
                return $messages[$key];
            }
        }

        return sprintf('%s یک خطای ناشناخته در سیستم رخ داده است', 'خطای 910: ');
    }

    private function getSuperSet(
        ExerciseBodyCategory $category,
        Exercise $exercise,
        ExercisePlanStrategyGenderEnum $gender,
        ExercisePlanStrategyLevelEnum $level,
        ExercisePlanStrategyDetailTypeEnum $type,
        array $usedExerciseId = [],
        array $userDiseases = null,
    ): Exercise|string {
        $usedExerciseIds = array_merge($usedExerciseId, [$exercise->id]);
        $superSet = $category->exercises()->main()->gender($gender)->level($level)->type($type)
            ->when($exercise->superCanNotExercises()->exists(), function ($query) use ($exercise) {
                return $query->whereNotIn('id', $exercise->superCanNotExercises()->pluck('id'));
            })
            ->when(is_array($userDiseases) && count($userDiseases), function ($query) use ($userDiseases) {
                return $query->whereDoesntHave('canNotDiseases', function ($query) use ($userDiseases) {
                    return $query->whereIn('diseases.id', $userDiseases);
                });
            })->whereNotIn('id', $usedExerciseIds)->get();

        if ($superSet->isNotEmpty()) {
            return $superSet->random();
        }

        $errors = [
            $category->exercises()->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)
                ->when($exercise->superCanNotExercises()->exists(), function ($query) use ($exercise) {
                    return $query->whereNotIn('id', $exercise->superCanNotExercises()->pluck('id'));
                })->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)
                ->when($exercise->superCanNotExercises()->exists(), function ($query) use ($exercise) {
                    return $query->whereNotIn('id', $exercise->superCanNotExercises()->pluck('id'));
                })
                ->when(is_array($userDiseases) && count($userDiseases), function ($query) use ($userDiseases) {
                    return $query->whereDoesntHave('canNotDiseases', function ($query) use ($userDiseases) {
                        return $query->whereIn('diseases.id', $userDiseases);
                    });
                })->exists(),
            $category->exercises()->main()->gender($gender)->level($level)->type($type)
                ->when($exercise->superCanNotExercises()->exists(), function ($query) use ($exercise) {
                    return $query->whereNotIn('id', $exercise->superCanNotExercises()->pluck('id'));
                })
                ->when(is_array($userDiseases) && count($userDiseases), function ($query) use ($userDiseases) {
                    return $query->whereDoesntHave('canNotDiseases', function ($query) use ($userDiseases) {
                        return $query->whereIn('diseases.id', $userDiseases);
                    });
                })->whereNotIn('id', $usedExerciseIds)->exists(),
        ];

        $messages = [
            sprintf('%s در دسته « %s » تمرینی تعریف نشده است.', 'خطای 610: ', $category->name),
            sprintf('%s در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » تعریف نشده است.', 'خطای 620: ',
                $category->name, $type->getName(), $gender->getName(), $level->getName()),
            sprintf('%s در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » که قابل سوپر شدن با « %s » باشد تعریف نشده است.',
                'خطای 630: ', $category->name, $type->getName(), $gender->getName(), $level->getName(),
                $exercise->name),
            sprintf('%s در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » که ممنوعیت استفاده بیماری نداشته باشد، تعریف نشده است.',
                'خطای 640: ',
                $category->name, $type->getName(), $gender->getName(), $level->getName()),
            sprintf('%s تعداد تمرین تعریف شده در دسته « %s »-« %s » تمرینی برای جنسیت « %s » با سطح « %s » ، کافی نیست.',
                'خطای 650: ', $category->name, $type->getName(), $gender->getName(), $level->getName()),
        ];

        //return error message first false in error array
        foreach ($errors as $key => $item) {
            if (!$item) {
                return $messages[$key];
            }
        }

        return sprintf('%s یک خطای ناشناخته در سیستم رخ داده است', 'خطای 900: ');
    }

    private function getStrategy(
        ?int $user_id,
        ExercisePlanStrategyTargetEnum $target,
        ExercisePlanStrategyGenderEnum $gender,
        ExercisePlanStrategyLevelEnum $level
    ): ?ExercisePlanStrategy {
        //get all possible strategy according to user target, gender , level where plan is active
        $allPossibleStrategy = ExercisePlanStrategy::active()->whereTarget($target)->whereGender($gender)->whereLevel($level)->get();
        //check is there any possible strategy exists
        if ($allPossibleStrategy->isNotEmpty()) {
            //check user ID is not null
            if ($user_id) {
                //get user old plans strategy
                $userOldPlan = ExercisePlanRequest::whereUserId($user_id)->whereStatus(ExercisePlanRequestEnum::COMPLETED)->orderByDesc('id')->pluck('exercise_plan_strategy_id')->toArray();
            } else {
                //if user id not exists give empty array for user old plans strategy ID
                $userOldPlan = [];
            }
            //if user has not any old plan give first item of all possible strategy
            if (count($userOldPlan) === 0) {
                return $allPossibleStrategy->first();
            }
            //intersect user old plan and all possible strategy in order to find new strategy
            $intersect = $allPossibleStrategy->pluck('id')->intersect($userOldPlan);
            //check intersect plans is not empty
            if ($intersect->isNotEmpty()) {
                //get first item of intersect
                $strategy = $allPossibleStrategy->where('id', $intersect->first())->first();
            } else {
                //give latest plan to user
                $strategy = $allPossibleStrategy->sortBy(function ($item) use ($userOldPlan) {
                    return array_search($item->id, $userOldPlan);
                })->last();
            }

            return $strategy;
        }

        return null;
    }
}
