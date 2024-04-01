<?php

declare(strict_types=1);

namespace Ldi\LogViewer;


use Arcanedev\Support\Providers\PackageServiceProvider;

class LogViewerServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'log-viewer';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->registerProvider(Providers\RouteServiceProvider::class);

        $this->registerCommands([
            Commands\PublishCommand::class,
            Commands\CheckCommand::class,
            Commands\ClearCommand::class,
        ]);
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->loadTranslations();
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishTranslations();
        }
    }
}
