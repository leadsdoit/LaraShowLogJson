<?php

return [

    'storage-path'  => storage_path('logs'),

    'pattern'       => [
        'prefix'    => 'laravel-',
        'date'      => '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]',
        'extension' => '.log',
    ],

    'locale'        => 'auto',

    'route'         => [
        'enabled'    => true,
        'attributes' => [
            'prefix'     => 'log-viewer/logs',
            'middleware' => env('LDI_LOGSPAVIEWER_MIDDLEWARE') ? explode(',', env('LDI_LOGSPAVIEWER_MIDDLEWARE')) : null,
        ],
    ],

    'per-page'      => 30,

    'download'      => [
        'prefix'    => 'laravel-',
        'extension' => 'log',
    ],

];
