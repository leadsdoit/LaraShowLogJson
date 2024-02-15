<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

use Ldi\LogViewer\LogViewerServiceProvider;
use Symfony\Component\Console\Input\InputOption;

class PublishCommand extends Command
{
    protected $name = 'log-viewer:publish';

    protected $description = 'Publish all LogViewer resources and config files';

    protected $signature = 'log-viewer:publish
            {--tag= : Tag(s) having assets needed to be published.}
            {--force : Overwrite any existing files.}';

    public function handle(): int
    {
        $args = [
            '--provider' => LogViewerServiceProvider::class,
        ];

        if ((bool) $this->option('force')) {
            $args['--force'] = true;
        }

        $args['--tag'] = [$this->option('tag')];

        $this->displayLogViewer();
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
