# Configuration 

---
**NOTE**
After publishing configs, you can find log-viewer.php file in your application's config directory.
---

# Storage Path
Storage path to get files from
```php
<?php

return [

    'storage-path'  => storage_path('logs'),

    // ...
];

```

# Pattern
Log files pattern.

```php
<?php

return [
    // ...
    'pattern'       => [
        'prefix'    => 'laravel-',
        'date'      => '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]',
        'extension' => '.log',
    ],
    
    // ...
];
```

# Routes
Route settings
```php
<?php

return [
    // ...

    'route'         => [
        'enabled'    => true,

        'attributes' => [
            'prefix'     => 'log-viewer',

            'middleware' => env('LDI_LOGVIEWER_MIDDLEWARE') ? explode(',', env('LDI_LOGVIEWER_MIDDLEWARE')) : null,
        ],
    ],
    
    // ...
];
```
By default no middleware is added to log-viewer route. If you need middlewares you just have to add a LDI_LOGVIEWER_MIDDLEWARE key to your .env file and add middlewares as comma separated values (no space).

```php
// Example
LDI_LOGVIEWER_MIDDLEWARE=api,auth
```

# Pagination
Log entries per page. 
```php
<?php

return [
    // ...
    'per-page'      => 30,
    
    // ...
];
```

# Download
Download settings

```php
<?php

return [

     // ...
    'download'      => [
        'prefix'    => 'laravel-',

        'extension' => 'log',
    ],

    // ...
];
```
