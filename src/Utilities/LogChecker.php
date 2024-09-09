<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Utilities;

use Ldi\LogSpaViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogSpaViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class LogChecker implements LogCheckerContract
{
    private ConfigContract $config;

    private FilesystemContract $filesystem;

    protected string $handler = '';

    private bool $status = true;

    private array $messages;

    private array $files = [];

    public function __construct(ConfigContract $config, FilesystemContract $filesystem)
    {
        $this->setConfig($config);
        $this->setFilesystem($filesystem);
        $this->refresh();
    }

    public function setConfig(ConfigContract $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function setFilesystem(FilesystemContract $filesystem): self
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    protected function setHandler($handler): self
    {
        $this->handler = strtolower($handler);

        return $this;
    }

    public function messages(): array
    {
        $this->refresh();

        return $this->messages;
    }

    public function passes(): bool
    {
        $this->refresh();

        return $this->status;
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function requirements(): array
    {
        $this->refresh();

        return $this->isDaily() ? [
            'status'  => 'success',
            'header'  => 'Application requirements fulfilled.',
            'message' => 'Are you ready to rock ?',
        ] : [
            'status'  => 'failed',
            'header'  => 'Application requirements failed.',
            'message' => $this->messages['handler']
        ];
    }

    protected function isDaily(): bool
    {
        return $this->isSameHandler(self::HANDLER_DAILY);
    }

    private function isSameHandler(string $handler): bool
    {
        return $this->handler === $handler;
    }

    private function refresh(): self
    {
        $this->setHandler($this->config->get('logging.default', 'stack'));

        $this->messages = [
            'handler' => '',
            'files'   => [],
        ];
        $this->files    = [];

        $this->checkHandler();
        $this->checkLogFiles();

        return $this;
    }

    private function checkHandler(): void
    {
        if ($this->isDaily()) return;

        $this->messages['handler'] = 'You should set the log handler to `daily` mode.';
    }

    private function checkLogFiles(): void
    {
        foreach ($this->filesystem->all() as $path) {
            $this->checkLogFile($path);
        }
    }

    private function checkLogFile(string $path): void
    {
        $status   = true;
        $filename = basename($path);
        $message  = "The log file [$filename] is valid.";
        $pattern  = $this->filesystem->getPattern();

        if ($this->isSingleLogFile($filename)) {
            $this->status = $status = false;
            $this->messages['files'][$filename] = $message =
                "You have a single log file in your application, you should split the [$filename] into separate log files.";
        }
        elseif ($this->isInvalidLogPattern($filename, $pattern)) {
            $this->status = $status = false;
            $this->messages['files'][$filename] = $message =
                "The log file [$filename] has an invalid date, the format must be like {$pattern}.";
        }

        $this->files[$filename] = compact('filename', 'status', 'message', 'path');
    }

    private function isSingleLogFile(string $file): bool
    {
        return $file === 'laravel.log';
    }

    private function isInvalidLogPattern(string $file, string $pattern): bool
    {
        return ((bool) preg_match("/{$pattern}/", $file, $matches)) === false;
    }
}
