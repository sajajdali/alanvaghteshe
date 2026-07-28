<?php

return [
    'name' => 'Setting',
    'permission' => [
        [
            'gate' => [
                'setting' => 'دسترسی به بخش تنظیمات',
            ],
            'type' => 'danger',
            'display_name' => 'بخش تنظیمات',
            'permissions' => [
            ]
        ],
    ],
    'menu' => [
        'title' => 'تنظیمات',
        'gate' => ['setting'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 10,
        'children' => [//it is required
            [
                'title' => 'تنظیمات',
                'gate' => 'setting',
                'policy_class' => null,
                'icon' => 'fe fe-settings',
                'route' => 'admin.setting',
                'has_badge' => false,
                'has_child' => false,
                'children' => null
            ],
        ],
    ],

    'setting' => require_once __DIR__ . '/setting.php'
];
