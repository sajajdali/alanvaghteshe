<?php

return [
    'name' => 'Package',

    'permission' => [
        [
            'gate' => [
                'package' => 'دسترسی به بسته‌ها',
            ],
            'type' => 'success',
            'display_name' => 'بسته‌ها',
            'permissions' => [
                'package.create' => 'ایجاد بسته',
                'package.edit' => 'ویرایش بسته',
                'package.delete' => 'حذف بسته',
            ],
        ],
    ],


    'menu' => [
        'title' => 'پکیج‌ها',
        'gate' => [
            'package',
        ],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 90,
        'children' => [//it is required
            [
                'title' => 'بسته‌ها',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Package\Entities\Package::class,
                'icon' => 'fe fe-grid',
                'route' => 'admin.package.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
            [
                'title' => 'بسته های خریداری شده ',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Package\Entities\Package::class,
                'icon' => 'fe fe-briefcase',
                'route' => 'admin.package.purchased',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
        ],
    ],
];
