<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Commands;

use Ldi\LogViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Ldi\LogViewer\Utilities\LogChecker;

class CheckCommand extends Command
{
    protected $name = 'log-viewer:check';

    protected $description = 'Check package requirements.';

    protected $signature = 'log-viewer:check';

    protected function getChecker(): LogChecker
    {
        return $this->laravel[LogCheckerContract::class];
    }

    public function handle(): int
    {
        $this->displayRequirements();
        $this->displayMessages();

        return static::SUCCESS;
    }

    private function displayRequirements(): void
    {
        $requirements = $this->getChecker()->requirements();

        $this->frame('Package requirements');

        $this->table([
            'Status', 'Message'
        ], [
            [$requirements['status'], $requirements['message']]
        ]);
    }

    private function displayMessages(): void
    {
        $messages = $this->getChecker()->messages();

        $rows = [];
        foreach ($messages['files'] as $file => $message) {
            $rows[] = [$file, $message];
        }

        if ( ! empty($rows)) {
            $this->frame('File info');
            $this->table(['File', 'Message'], $rows);
        }
    }
}
