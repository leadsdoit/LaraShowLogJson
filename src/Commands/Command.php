<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

use Arcanedev\Support\Console\Command as BaseCommand;
use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;

abstract class Command extends BaseCommand
{
    protected LogViewerContract $logViewer;

    public function __construct(LogViewerContract $logViewer)
    {
        parent::__construct();

        $this->logViewer = $logViewer;
    }

}
