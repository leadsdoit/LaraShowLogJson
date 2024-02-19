<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Providers;

use Ldi\LogViewer\Http\Routes\LogViewerRoute;
use Ldi\Support\Providers\RouteServiceProvider as ServiceProvider;


class RouteServiceProvider extends ServiceProvider
{
    public function isEnabled(): bool
    {
        return (bool) $this->config('enabled', false);
    }

    public function boot(): void
    {
        if ($this->isEnabled()) {
            $this->routes(function () {
                static::mapRouteClasses([LogViewerRoute::class]);
            });
        }
    }

    private function config($key, $default = null)
    {
        return $this->app['config']->get("log-viewer.route.$key", $default);
    }
}
