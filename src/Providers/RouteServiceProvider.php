<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    public function isEnabled(): bool
    {
        return (bool) $this->config('enabled', false);
    }

    public function boot(): void
    {
        if ($this->isEnabled()) {
            $attributes = $this->config('attributes', []);
            Route::group($attributes, fn() => $this->loadRoutesFrom(__DIR__.'/../routes.php'));
        }
    }

    private function config($key, $default = null)
    {
        return $this->app['config']->get("log-spa-viewer.route.$key", $default);
    }
}
