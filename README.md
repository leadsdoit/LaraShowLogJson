# Log SPA Viewer

A Laravel Package for Log File Parsing for SPA.

**Inspired by [ARCANEDEV/LogViewer](https://github.com/ARCANEDEV/LogViewer)**


## Installation

You can install this package via Composer by running this command:

```shell
    composer require ldi/log-viewer
```


## Commands
1. To publish the config and translations files, run this command:

```shell
    php artisan log-spa-viewer:publish
```

2. To force publishing

```shell
    php artisan log-spa-viewer:publish --force
```

3. Publishing the config only

```shell
    php artisan log-spa-viewer:publish --tag=config
```

4. Application requirements & log files check

```shell
    php artisan log-spa-viewer:check
```

# Configuration 

See the [Configuration](docs/configuration.md) page to finish installation.

# Endpoints

Also see the [Endpoints](docs/routes.md) 
