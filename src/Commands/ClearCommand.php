<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

class ClearCommand extends Command
{
    protected $signature = 'log-viewer:clear';

    protected $description = 'Clear all generated log files';

    public function handle(): int
    {
        if ($this->confirm('This will delete all the log files, Do you wish to continue?')) {
            $this->logViewer->clear();
            $this->info('Successfully cleared the logs!');
        }

        return static::SUCCESS;
    }
}
