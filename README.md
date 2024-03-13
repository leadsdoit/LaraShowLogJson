# About

A Laravel Package for Log File Parsing.

## Installation

1. Add GitLab/GitHub repository links in composer.json file.

```php
  "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/..../ldi-log"
        },
        {
            "type": "vcs",
            "url": "https://github.com/..../ldi-support"
        }
    ],
```

2. You can install this package via Composer by running this command:

```php
    composer require ldi/log-viewer:{x.x} 
```

where x.x is the last version of package compatible with your laravel's version..

## Commands
1. To publish the config and translations files, run this command:

```php
    php artisan log-viewer:publish
```

2. To force publishing

```php
    php artisan log-viewer:publish --force
```

3. Publishing the config only

```
    php artisan log-viewer:publish --tag=config
```

4. Application requirements & log files check

```
    php artisan log-viewer:check
```

# Configuration 

See the [Configuration](docs/configuration.md) page to finish installation.

# Endpoints

Also see the [Endpoints](docs/routes.md) 
