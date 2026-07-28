<?php

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Commands;

return [

    'namespace' => 'Modules',

    'stubs' => [
        'enabled' => false,
        'path' => base_path('vendor/nwidart/laravel-modules/src/Commands/stubs'),
        'files' => [
            'routes/web' => 'routes/web.php',
            'routes/api' => 'routes/api.php',
            'views/index' => 'resources/views/index.blade.php',
            'views/master' => 'resources/views/layouts/master.blade.php',
            'scaffold/config' => 'config/config.php',
            'composer' => 'composer.json',
            'assets/js/app' => 'resources/assets/js/app.js',
            'assets/sass/app' => 'resources/assets/sass/app.scss',
            'vite' => 'vite.config.js',
            'package' => 'package.json',
        ],
        'replacements' => [
            'routes/web' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'CONTROLLER_NAMESPACE'],
            'routes/api' => ['LOWER_NAME', 'STUDLY_NAME'],
            'vite' => ['LOWER_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'views/index' => ['LOWER_NAME'],
            'views/master' => ['LOWER_NAME', 'STUDLY_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
                'PROVIDER_NAMESPACE',
            ],
        ],
        'gitkeep' => true,
    ],

    'paths' => [
        'modules' => base_path('Modules'),
        'assets' => public_path('modules'),
        'migration' => base_path('database/migrations'),
        'generator' => [
            'config' => ['path' => 'config', 'generate' => true],
            'command' => ['path' => 'app/Console', 'generate' => false],
            'channels' => ['path' => 'app/Broadcasting', 'generate' => false],
            'migration' => ['path' => 'database/migrations', 'generate' => true],
            'seeder' => ['path' => 'database/seeders', 'namespace' => 'Database\\Seeders', 'generate' => true],
            'factory' => ['path' => 'database/factories', 'generate' => true],
            'model' => ['path' => 'app/Models', 'generate' => true],
            'observer' => ['path' => 'app/Observers', 'generate' => false],
            'routes' => ['path' => 'routes', 'generate' => true],
            'controller' => ['path' => 'Http/Controllers', 'generate' => true],
            'filter' => ['path' => 'app/Http/Middleware', 'generate' => true],
            'request' => ['path' => 'app/Http/Requests', 'generate' => true],
            'provider' => ['path' => 'app/Providers', 'generate' => true],
            'assets' => ['path' => 'resources/assets', 'generate' => true],
            'lang' => ['path' => 'lang', 'generate' => true],
            'views' => ['path' => 'resources/views', 'generate' => true],
            'test' => ['path' => 'tests/Unit', 'generate' => true],
            'test-feature' => ['path' => 'tests/Feature', 'generate' => true],
            'repository' => ['path' => 'app/Repositories', 'generate' => false],
            'event' => ['path' => 'app/Events', 'generate' => false],
            'listener' => ['path' => 'app/Listeners', 'generate' => false],
            'policies' => ['path' => 'app/Policies', 'generate' => false],
            'rules' => ['path' => 'app/Rules', 'generate' => false],
            'jobs' => ['path' => 'app/Jobs', 'generate' => false],
            'emails' => ['path' => 'app/Emails', 'generate' => false],
            'notifications' => ['path' => 'app/Notifications', 'generate' => false],
            'resource' => ['path' => 'app/Resources', 'generate' => false],
            'component-view' => ['path' => 'resources/views/components', 'generate' => false],
            'component-class' => ['path' => 'app/View/Components', 'generate' => false],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    | فقط اون‌هایی که وجود دارن لود میشن
    */
    'commands' => array_values(array_filter([
        class_exists(Commands\ControllerMakeCommand::class) ? Commands\ControllerMakeCommand::class : null,
        class_exists(Commands\EnableCommand::class) ? Commands\EnableCommand::class : null,
        class_exists(Commands\DisableCommand::class) ? Commands\DisableCommand::class : null,
        class_exists(Commands\ListCommand::class) ? Commands\ListCommand::class : null,
        class_exists(Commands\ModuleMakeCommand::class) ? Commands\ModuleMakeCommand::class : null,
        class_exists(Commands\MigrateCommand::class) ? Commands\MigrateCommand::class : null,
        class_exists(Commands\MigrateRollbackCommand::class) ? Commands\MigrateRollbackCommand::class : null,
        class_exists(Commands\MigrationMakeCommand::class) ? Commands\MigrationMakeCommand::class : null,
        class_exists(Commands\ModelMakeCommand::class) ? Commands\ModelMakeCommand::class : null,
        class_exists(Commands\SeedMakeCommand::class) ? Commands\SeedMakeCommand::class : null,
        class_exists(Commands\SeedCommand::class) ? Commands\SeedCommand::class : null,
        // هر چی لازم داشتی اضافه کن
    ])),

    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],

    'composer' => [
        'vendor' => 'nwidart',
        'author' => [
            'name' => 'Nicolas Widart',
            'email' => 'n.widart@gmail.com',
        ],
        'composer-output' => false,
    ],

    'cache' => [
        'enabled' => false,
        'driver' => 'file',
        'key' => 'laravel-modules',
        'lifetime' => 60,
    ],

    'register' => [
        'translations' => true,
        'files' => 'register',
    ],

    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],

    'activator' => 'file',
];
