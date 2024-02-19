<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

class ClearCommand extends Command
{
    protected $signature = 'log-viewer:clear';

    protected $description = 'Remove generated log files';

    public function handle(): int
    {
        if ($this->confirm('Remove all log files?')) {
            $this->logViewer->clear();
            $this->info('Logs were successfully removed!');
        }

        return static::SUCCESS;
    }
}
