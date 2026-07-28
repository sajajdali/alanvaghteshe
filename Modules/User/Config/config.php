<?php

return [
    'name' => 'User',

    'permission' => [
        [
            'gate' => [
                'role' => 'دسترسی به نقش‌ها',
            ],
            'type' => 'success',
            'display_name' => 'نقش‌ها',
            'permissions' => [
                'role.create' => 'ایجاد نقش',
                'role.edit' => 'ویرایش نقش',
                'role.delete' => 'حذف نقش',
            ],
        ],
        [
            'gate' => [
                'user' => 'همه کاربران',
                'user.own' => 'کاربران خود',
            ],
            'type' => 'danger',
            'display_name' => 'کاربران',
            'permissions' => [
                'user.create' => 'ایجاد کاربر',
                'user.edit' => 'ویرایش کاربر',
                'user.delete' => 'حذف کاربر',
                'user.documentte' => 'مشاهده پرونده',
                'user.sendAppLink' => 'ارسال لینک اپ',
            ],
        ],
    ],

    'menu' => [
        'title' => 'بخش کاربری',
        'gate' => ['user', 'user.own', 'role'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 100,
        'children' => [//it is required
            [
                'title' => 'کاربران',
                'gate' => 'viewAny',
                'policy_class' => \Modules\User\Entities\User::class,
                'icon' => 'fe fe-user',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\User\Entities\User::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.user.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\User\Entities\User::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.user.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'referral',
                        'gate' => 'create',
                        'policy_class' => \Modules\User\Entities\User::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.user.referral.index',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
            [
                'title' => 'نقش‌ها',
                'gate' => 'viewAny',
                'policy_class' => \Spatie\Permission\Models\Role::class,
                'icon' => 'fe fe-award',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.role.index',
                        'policy_class' => \Spatie\Permission\Models\Role::class,
                        'has_child' => false,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.role.create',
                        'policy_class' => \Spatie\Permission\Models\Role::class,
                        'has_child' => false,
                    ],
                ],
            ],
        ],
    ],
];
