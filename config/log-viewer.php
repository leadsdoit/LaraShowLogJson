<?php

use Ldi\LogViewer\Contracts\Utilities\Filesystem;

return [

    'storage-path'  => storage_path('logs'),

    'pattern'       => [
        'prefix'    => Filesystem::PATTERN_PREFIX,    // 'laravel-'
        'date'      => Filesystem::PATTERN_DATE,      // '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]'
        'extension' => Filesystem::PATTERN_EXTENSION, // '.log'
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
