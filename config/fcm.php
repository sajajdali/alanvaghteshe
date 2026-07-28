<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Firebase Project
    |--------------------------------------------------------------------------
    |
    | The default project to use for FCM. Must match one of the keys in
    | the "projects" array below.
    |
    */

    'default' => env('FIREBASE_PROJECT_ID', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Projects
    |--------------------------------------------------------------------------
    |
    | Define one or more Firebase projects and credentials for each.
    | Place your downloaded service account JSON file in storage/app.
    |
    */

    'projects' => [

        env('FIREBASE_PROJECT_ID', 'default') => [
            'credentials' => [
                'file' => env('FIREBASE_CREDENTIALS'),
            ],
        ],

    ],
];
