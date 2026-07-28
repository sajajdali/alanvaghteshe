<?php

return [
    'name' => 'Chat',
    'permission' => [
        [
            'gate' => [
                'chat' => 'بخش گفت و گو',
            ],
            'type' => 'warning',
            'display_name' => 'بخش گفت و گو',
            'permissions' => [
                'admin.chat.list' => 'بخش گفت و گو',
            ],
        ]
    ],
];
