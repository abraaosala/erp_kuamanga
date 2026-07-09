<?php

return [
    'name'    => $_ENV['APP_NAME'] ?? 'ERP Sistema',
    'env'     => $_ENV['APP_ENV'] ?? 'production',
    'debug'   => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url'     => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'key'     => $_ENV['APP_KEY'] ?? '',
    'timezone' => 'Africa/Luanda',
    'locale'   => 'pt_BR',

    'providers' => [
        App\Providers\Modules\User\AuthServiceProvider::class,
        App\Providers\Modules\User\UserServiceProvider::class,
        App\Providers\Modules\Accounting\AccountingServiceProvider::class,
        App\Providers\Modules\Rh\RhServiceProvider::class,
    ],

    'session' => [
        'driver'   => $_ENV['SESSION_DRIVER'] ?? 'file',
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
        'path'     => __DIR__ . '/../storage/cache',
    ],
];
