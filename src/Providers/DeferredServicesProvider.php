<?php

declare(strict_types = 1);

namespace Ldi\LogSpaViewer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Ldi\LogSpaViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogSpaViewer\Contracts\Utilities\Factory as FactoryContract;
use Ldi\LogSpaViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogSpaViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Ldi\LogSpaViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Ldi\LogSpaViewer\LogSpaViewer;
use Ldi\LogSpaViewer\Utilities;

class DeferredServicesProvider extends ServiceProvider implements DeferrableProvider
{

    public function register(): void
    {
        $this->app->singleton(LogViewerContract::class, LogSpaViewer::class);
        $this->app->singleton(LogLevelsContract::class, function ($app) {
            return new Utilities\LogLevels(
                $app['translator'],
                $app['config']->get('log-spa-viewer.locale')
            );
        });

        $this->app->singleton(FilesystemContract::class, function ($app) {
            return new Utilities\Filesystem(
                $app['files'],
                $app['config']->get('log-spa-viewer.storage-path')
            );
        });

        $this->app->singleton(FactoryContract::class, Utilities\Factory::class);
        $this->app->singleton(LogCheckerContract::class, Utilities\LogChecker::class);
    }

    public function provides(): array
    {
        return [
            LogViewerContract::class,
            LogLevelsContract::class,
            FilesystemContract::class,
            FactoryContract::class,
            LogCheckerContract::class,
        ];
    }

}
