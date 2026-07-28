<?php

return [
    'name' => 'Exercise',

    'permission' => [
        [
            'gate' => [
                'exercise' => 'دسترسی به ورزش‌ها',
            ],
            'type' => 'success',
            'display_name' => 'ورزش‌ها',
            'permissions' => [
                'exercise.create' => 'ایجاد ورزش',
                'exercise.edit' => 'ویرایش ورزش',
                'exercise.delete' => 'حذف ورزش',
            ],
        ],
        [
            'gate' => [
                'exercise_body_category' => 'دسترسی به بخش بندی بدن',
            ],
            'type' => 'success',
            'display_name' => 'دسته‌بندی‌ها',
            'permissions' => [
                'exercise_body_category.create' => 'ایجاد دسته‌بندی',
                'exercise_body_category.edit' => 'ویرایش دسته‌بندی',
                'exercise_body_category.delete' => 'حذف دسته‌بندی',
            ],
        ],
        [
            'gate' => [
                'exercise_plan_strategy' => 'دسترسی به پلان‌های ورزشی',
            ],
            'type' => 'success',
            'display_name' => 'پلان‌های ورزشی',
            'permissions' => [
                'exercise_plan_strategy.create' => 'ایجاد پلان ورزشی',
                'exercise_plan_strategy.edit' => 'ویرایش پلان ورزشی',
                'exercise_plan_strategy.delete' => 'حذف پلان ورزشی',
            ],
        ],
        [
            'gate' => [
                'exercise_plan_request' => 'دسترسی به همه برنامه‌های ورزشی',
                'exercise_plan_request.own' => 'دسترسی به برنامه‌های ورزشی کاربران خود',
            ],
            'type' => 'success',
            'display_name' => 'درخواست برنامه ورزشی',
            'permissions' => [
                'exercise_plan_request.create' => 'ایجاد درخواست برنامه',
                'exercise_plan_request.edit' => 'ویرایش برنامه',
                'exercise_plan_request.delete' => 'حذف برنامه ورزشی',
            ],
        ],
    ],


    'menu' => [
        'title' => 'بخش ورشی',
        'gate' => ['exercise', 'exercise_body_category', 'exercise_plan_strategy','exercise_plan_request','exercise_plan_request.own'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 90,
        'children' => [//it is required
            [
                'title' => 'ورزش‌ها',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Exercise\Entities\Exercise::class,
                'icon' => 'fe fe-aperture',
                'route' => 'admin.exercise.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
            [
                'title' => 'دسته‌بندی بدن',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Exercise\Entities\ExerciseBodyCategory::class,
                'icon' => 'fe fe-layers',
                'route' => 'admin.exercise_body_category.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
            [
                'title' => 'پلان های ورزشی',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Exercise\Entities\ExercisePlanStrategy::class,
                'icon' => 'fe fe-layout',
                'route' => 'admin.exercise_plan_strategy.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
            [
                'title' => 'درخواست‌های برنامه ورزشی',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Exercise\Entities\ExercisePlanRequest::class,
                'icon' => 'fe fe-users',
                'route' => 'admin.exercise_plan_request.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
        ],
    ],

];
