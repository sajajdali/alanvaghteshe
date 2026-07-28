<?php

return [
    'name' => 'transactions',
    'permission' => [
        [
            'gate' => [
                'admin.transaction' => 'مدیریت تراکنش ها',
            ],
            'type' => 'warning',
            'display_name' => 'مدیریت تراکنش ها',
            'permissions' => [
                'admin.transaction' => 'مشاهده تراکنش ها',
            ],
        ],
    ],
];
