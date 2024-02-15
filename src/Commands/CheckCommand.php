<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

use Ldi\LogViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Ldi\LogViewer\Utilities\LogChecker;

class CheckCommand extends Command
{
    protected $name = 'log-viewer:check';

    protected $description = 'Check all LogViewer requirements.';

    protected $signature = 'log-viewer:check';

    /**
     * Get the Log Checker instance.
     */
    protected function getChecker(): LogChecker
    {
        return $this->laravel[LogCheckerContract::class];
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->displayLogViewer();
        $this->displayRequirements();
        $this->displayMessages();

        return static::SUCCESS;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Display LogViewer requirements.
     */
    private function displayRequirements()
    {
        $requirements = $this->getChecker()->requirements();

        $this->frame('Application requirements');

        $this->table([
            'Status', 'Message'
        ], [
            [$requirements['status'], $requirements['message']]
        ]);
    }

    /**
     * Display LogViewer messages.
     */
    private function displayMessages()
    {
        $messages = $this->getChecker()->messages();

        $rows = [];
        foreach ($messages['files'] as $file => $message) {
            $rows[] = [$file, $message];
        }

        if ( ! empty($rows)) {
            $this->frame('LogViewer messages');
            $this->table(['File', 'Message'], $rows);
        }
    }
}
