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
            'prefix'     => 'log-viewer',
            'middleware' => env('LDI_LOGVIEWER_MIDDLEWARE') ? explode(',', env('LDI_LOGVIEWER_MIDDLEWARE')) : null,
        ],
    ],

    'per-page'      => 30,

    'download'      => [
        'prefix'    => 'laravel-',
        'extension' => 'log',
    ],

];
