<?php

return [
    'name' => 'Reminder',
    'permission' => [
        [
            'gate' => [
                'admin.reminder' => '',
            ],
            'type' => 'warning',
            'display_name' => 'یادآوری',
            'permissions' => [
                'admin.dashboard.update' => ' مدیریت یادآوری ها',
                'admin.dashboard.delete' => 'حذف یادآوری',
            ],
        ],
    ],

    'menu' => [
        'title' => 'یادآوری ها',
        'gate' => ['reminder'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 60,
        'children' => [//it is required
            [
                'title' => 'یادآوری ها',
                'gate' => 'viewAny',
                'policy_class' => \Modules\User\Entities\User::class,
                'icon' => 'fe fe-send',
                'route' => null,
                'has_badge' => false,
                'has_child' => true,
                'children' => [
                    [
                        'title' => 'لیست',
                        'gate' => 'viewAny',
                        'policy_class' => \Modules\User\Entities\User::class,
                        'icon' => 'fa fa-list',
                        'route' => 'admin.reminder.list',
                        'has_child' => false,
                        'children' => null,
                    ],
                    [
                        'title' => 'افزودن',
                        'gate' => 'create',
                        'policy_class' => \Modules\User\Entities\User::class,
                        'icon' => 'fa fa-plus-circle',
                        'route' => 'admin.reminder.create',
                        'has_child' => false,
                        'children' => null,
                    ],
                ],
            ],
        ],
    ],
];
