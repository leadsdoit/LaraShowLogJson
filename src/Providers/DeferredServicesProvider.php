<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Providers;

use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogViewer\Contracts\Utilities\Factory as FactoryContract;
use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Ldi\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Ldi\LogViewer\LogViewer;
use Ldi\LogViewer\Utilities;
use Illuminate\Contracts\Support\DeferrableProvider;
use  support\Providers\ServiceProvider;

/**
 * Class     DeferredServicesProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DeferredServicesProvider extends ServiceProvider implements DeferrableProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerLogViewer();
        $this->registerLogLevels();
        $this->registerFilesystem();
        $this->registerFactory();
        $this->registerChecker();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
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

    /* -----------------------------------------------------------------
     |  LogViewer Utilities
     | -----------------------------------------------------------------
     */

    /**
     * Register the log viewer service.
     */
    private function registerLogViewer(): void
    {
        $this->singleton(LogViewerContract::class, LogViewer::class);
    }

    /**
     * Register the log levels.
     */
    private function registerLogLevels(): void
    {
        $this->singleton(LogLevelsContract::class, function ($app) {
            return new Utilities\LogLevels(
                $app['translator'],
                $app['config']->get('log-viewer.locale')
            );
        });
    }

    /**
     * Register the log filesystem.
     */
    private function registerFilesystem(): void
    {
        $this->singleton(FilesystemContract::class, function ($app) {
            /** @var  \Illuminate\Config\Repository  $config */
            $config     = $app['config'];
            $filesystem = new Utilities\Filesystem($app['files'], $config->get('log-viewer.storage-path'));

            return $filesystem->setPattern(
                $config->get('log-viewer.pattern.prefix', FilesystemContract::PATTERN_PREFIX),
                $config->get('log-viewer.pattern.date', FilesystemContract::PATTERN_DATE),
                $config->get('log-viewer.pattern.extension', FilesystemContract::PATTERN_EXTENSION)
            );
        });
    }

    /**
     * Register the log factory class.
     */
    private function registerFactory(): void
    {
        $this->singleton(FactoryContract::class, Utilities\Factory::class);
    }

    /**
     * Register the log checker service.
     */
    private function registerChecker(): void
    {
        $this->singleton(LogCheckerContract::class, Utilities\LogChecker::class);
    }
}
