<?php

return [
    'name' => 'Onboarding',

    'permission' => [
        [
            'gate' => [
                'onboarding' => 'مدیریت پیشنهاد آن‌بوردینگ',
            ],
            'type' => 'primary',
            'display_name' => 'آن‌بوردینگ اپلیکیشن',
            'permissions' => [],
        ],
    ],

    'menu' => [
        'title' => 'اپلیکیشن',
        'gate' => ['onboarding'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 65,
        'children' => [
            [
                'title' => 'آن‌بوردینگ',
                'gate' => 'onboarding',
                'policy_class' => null,
                'icon' => 'fe fe-gift',
                'route' => 'admin.onboarding.index',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
        ],
    ],
];
