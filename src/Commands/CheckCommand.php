<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Commands;

use Ldi\LogSpaViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Ldi\LogSpaViewer\Utilities\LogChecker;
use Illuminate\Console\Command;

class CheckCommand extends Command
{
    protected $name = 'log-spa-viewer:check';

    protected $description = 'Check package requirements.';

    protected $signature = 'log-spa-viewer:check';

    public function handle(): int
    {
        $this->displayRequirements();
        $this->displayMessages();

        return static::SUCCESS;
    }

    private function getChecker(): LogChecker
    {
        return $this->laravel[LogCheckerContract::class];
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


    private function frame(string $text): void
    {
        $line   = '+'.str_repeat('-', strlen($text) + 4).'+';
        $this->info($line);
        $this->info("|  $text  |");
        $this->info($line);
    }
}
