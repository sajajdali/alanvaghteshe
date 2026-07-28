<?php

use Modules\Diet\app\Models\NutritionTip;

return [
    'name' => 'Diet',

    'permission' => [
        [
            'gate' => [
                'meal' => 'دسترسی به وعده ها',
            ],
            'type' => 'success',
            'display_name' => 'بخش وعده ها',
            'permissions' => [
                'meal.create' => 'ایجاد وعده',
                'meal.edit'   => 'ویرایش وعده',
                'meal.delete' => 'حذف وعده',
            ],
        ],

        [
            'gate' => [
                'basic_food' => 'دسترسی به غذاهای پایه',
            ],
            'type' => 'success',
            'display_name' => 'بخش غذاهای پایه',
            'permissions' => [
                'basic_food.create' => 'ایجاد غذای پایه',
                'basic_food.edit'   => 'ویرایش غذای پایه',
                'basic_food.delete' => 'حذف غذای پایه',
            ],
        ],

        [
            'gate' => [
                'food_unit' => 'دسترسی به واحد غذاها',
            ],
            'type' => 'success',
            'display_name' => 'بخش واحد غذاها',
            'permissions' => [
                'food_unit.create' => 'ایجاد واحد غذایی',
                'food_unit.edit' => 'ویرایش واحد غذایی',
                'food_unit.delete' => 'حذف واحد غذایی',
            ],
        ],

        [
            'gate' => [
                'food' => 'دسترسی  غذاها',
            ],
            'type' => 'success',
            'display_name' => 'بخش  غذاها',
            'permissions' => [
                'food.create' => 'ایجاد  غذا',
                'food.edit'   => 'ویرایش  غذا',
                'food.delete' => 'حذف  غذا',
            ],
        ],
        [
            'gate' => [
                'requestDiet' => 'درخواست های رژیم',
            ],
            'type' => 'success',
            'display_name' => 'درخواست های رژیم',
            'permissions' => [
                'requestDiet.detail' => 'جزئیات رژیم',
            ],
        ],
        [
            'gate' => [
                'condition' => 'وضعیت',
            ],
            'type' => 'success',
            'display_name' => 'وضعیت ها ',
            'permissions' => [
                'condition.create' => 'ایجاد وضعیت',
                'condition.delete' => 'حذف وضعیت',
                'condition.edit' => 'ویرایش وضعیت',
            ],
        ],
        [
            'gate' => [
                'diet_plan' => 'برنامه غذایی',
            ],
            'type' => 'success',
            'display_name' => 'برنامه غذایی',
            'permissions' => [
                'diet_plan.create' => 'ایجاد وضعیت',
                'diet_plan.delete' => 'حذف وضعیت',
                'diet_plan.edit' => 'ویرایش وضعیت',
            ],
        ],

    ],

    'menu' => [
        'title' => 'بخش تغذیه',
        'gate' => ['meal', 'food', 'food_unit'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 91,
        'children' => [//it is required

            [
                'title' => 'وعده ها',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\Meal::class,
                'icon' => 'ion-leaf',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\Meal::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.meal.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\Meal::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.meal.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'واحد های غذایی',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\FoodUnit::class,
                'icon' => 'ion-aperture',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\FoodUnit::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.food_unit.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\FoodUnit::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.food_unit.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'غذاهای پایه',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\BasicFood::class,
                'icon' => 'fa fa-yelp',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\BasicFood::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.basic_food.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\BasicFood::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.basic_food.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],

            [
                'title' => 'غذاهای ترکیبی',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\Food::class,
                'icon' => 'zmdi zmdi-cutlery',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\Food::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.food.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\Food::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.food.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'شراط و ویژگی ها  ',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\Condition::class,
                'icon' => 'fa fa-steam',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\Condition::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.condition.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\Condition::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.condition.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'پلن های رژیم',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                'icon' => 'icon-calendar',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.diet_plan.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.diet_plan.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'رژیم های تجویز شده',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                'icon' => 'fe fe-file-plus',
                'route' => 'admin.presCribed-diets',
                'has_badge' => false,
                'has_child' => false,
                'children' => null

            ],
            [
                'title' => 'دستور غذا',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                'icon' => 'fe fe-thermometer',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.recipe.list',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\Diet\Entities\DietPlan::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.recipe.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => ' نکات تغذیه',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Diet\app\Models\NutritionTip::class,
                'icon' => 'fe fe-at-sign',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => NutritionTip::class,
                        'icon' => 'fe fe-copy',
                        'route' => 'admin.nutrition_tip.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => NutritionTip::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.nutrition_tip.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
        ],
    ],
];
