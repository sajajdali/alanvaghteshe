<?php

return [
    'name' => 'Coupon',
    'permission' => [
        [
            'gate' => [
                'admin.Coupon' => 'مدیریت کد تخفیف ها',
            ],
            'type' => 'warning',
            'display_name' => 'مدیریت کد تخفیف ها',
            'permissions' => [
                'admin.Coupon' => 'مدیریت کد تخفیف ها',
            ],
        ],
    ],
    'menu' => [
        'title' => 'مالی',
        'gate' => ['admin.Coupon'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 70,
        'children' => [//it is required
            [
                'title' => 'کد تخفیف',
                'gate' => 'viewAny',
                'policy_class' => null,
                'icon' => 'fa fa-plus-circle',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' =>null,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.coupon.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => null,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.coupon.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'تراکنش ها',
                'gate' => 'viewAny',
                'policy_class' => null,
                'icon' => 'fe fe-dollar-sign',
                'route' => 'admin.transaction.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null , 
            ],
        ],
    ],

];
