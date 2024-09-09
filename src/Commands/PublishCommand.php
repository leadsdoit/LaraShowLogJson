<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Commands;

use Ldi\LogSpaViewer\LogSpaViewerServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $name = 'log-spa-viewer:publish';

    protected $description = 'Publish all LogViewer resources and config files';

    protected $signature = 'log-spa-viewer:publish
            {--tag= : Tag(s) having assets needed to be published.}
            {--force : Overwrite any existing files.}';

    public function handle(): int
    {
        $args = [
            '--provider' => LogSpaViewerServiceProvider::class,
        ];

        if ($this->option('force')) {
            $args['--force'] = true;
        }

        $args['--tag'] = [$this->option('tag')];

        $this->call('vendor:publish', $args);

        return static::SUCCESS;
    }

    protected function getOptions(): array
    {
        return [
            ['tag', 't', InputOption::VALUE_OPTIONAL, 'Tag(s) having assets needed to be published.', ''],
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'Overwrite any existing files.', false],
        ];
    }
}
