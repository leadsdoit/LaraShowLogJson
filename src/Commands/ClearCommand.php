<?php

declare(strict_types = 1);

namespace Ldi\LogSpaViewer\Commands;

use Ldi\LogSpaViewer\Contracts\LogViewer as LogViewerContract;
use Illuminate\Console\Command;

class ClearCommand extends Command
{

    protected $signature = 'log-spa-viewer:clear';

    protected $description = 'Remove generated log files';

    public function handle(LogViewerContract $logViewer): int
    {
        if ($this->confirm('Remove all log files?')) {
            $logViewer->clear();
            $this->info('Logs were successfully removed!');
        }

        return static::SUCCESS;
    }

}
