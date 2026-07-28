<?php

return [
    'name' => 'Core',
    'permission' => [
        [
            'gate' => [
                'faq' => 'دسترسی به سوالات متداول اپلیکیشن',
            ],
            'type' => 'success',
            'display_name' => 'سوالات متداول اپلیکیشن',
            'permissions' => [
                'faq.create' => 'ایجاد سوال',
                'faq.edit' => 'ویرایش سوال',
                'faq.delete' => 'حذف سوال',
            ],
        ]
    ],
    'menu' => [
        'title' => 'پشتیبانی',
        'gate' => ['faq','chat'],
        'policy_class' => null,
        'has_divider' => true,
        'priority' => 90,
        'children' => [//it is required
            [
                'title' => 'گفت و گو',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Chat\app\Models\Chat::class,
                'icon' => 'fe fe-message-square',
                'route' => 'admin.chat',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ],
            [
                'title' => 'سوالات متداول اپلیکیشن',
                'gate' => 'viewAny',
                'policy_class' => \Modules\Core\Entities\Faq::class,
                'icon' => 'fe fe-anchor',
                'route' => 'admin.faq',
                'has_badge' => false,
                'has_child' => false,
                'children' => null,
            ]
        ],
    ],
];
