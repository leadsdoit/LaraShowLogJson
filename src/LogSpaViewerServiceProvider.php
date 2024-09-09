<?php

declare(strict_types = 1);

namespace Ldi\LogSpaViewer;


use Illuminate\Support\ServiceProvider;

class LogSpaViewerServiceProvider extends ServiceProvider
{

    protected $package = 'log-spa-viewer';

    public function register(): void
    {
        $this->mergeConfigFrom($this->fileConfig(), $this->package);

        $this->app->register(Providers\DeferredServicesProvider::class);
        $this->app->register(Providers\RouteServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\PublishCommand::class,
                Commands\CheckCommand::class,
                Commands\ClearCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        $this->publishes([
            $this->fileConfig() => config_path($this->package.'.php'),
        ]);
    }

    protected function fileConfig(): string
    {
        return __DIR__.'/../config/'.$this->package.'.php';
    }

}
