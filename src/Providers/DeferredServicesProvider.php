<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Providers;

use Arcanedev\Support\Providers\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogViewer\Contracts\Utilities\Factory as FactoryContract;
use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Ldi\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Ldi\LogViewer\LogViewer;
use Ldi\LogViewer\Utilities;

class DeferredServicesProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->registerLogViewer();
        $this->registerLogLevels();
        $this->registerFilesystem();
        $this->registerFactory();
        $this->registerChecker();
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

    private function registerLogViewer(): void
    {
        $this->singleton(LogViewerContract::class, LogViewer::class);
    }

    private function registerLogLevels(): void
    {
        $this->singleton(LogLevelsContract::class, function ($app) {
            return new Utilities\LogLevels(
                $app['translator'],
                $app['config']->get('log-viewer.locale')
            );
        });
    }

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

    private function registerFactory(): void
    {
        $this->singleton(FactoryContract::class, Utilities\Factory::class);
    }

    private function registerChecker(): void
    {
        $this->singleton(LogCheckerContract::class, Utilities\LogChecker::class);
    }
}
