<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use src\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected LogViewerContract $logViewer;

    public function __construct(LogViewerContract $logViewer)
    {
        parent::__construct();

        $this->logViewer = $logViewer;
    }

    protected function displayLogViewer() {}
}
