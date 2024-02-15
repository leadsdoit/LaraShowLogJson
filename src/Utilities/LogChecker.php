<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Utilities;

use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class LogChecker implements LogCheckerContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;

    /**
     * The filesystem instance.
     *
     * @var \Ldi\LogViewer\Contracts\Utilities\Filesystem
     */
    private $filesystem;

    /**
     * Log handler mode.
     *
     * @var string
     */
    protected $handler = '';

    /**
     * The check status.
     *
     * @var bool
     */
    private $status = true;

    /**
     * The check messages.
     *
     * @var array
     */
    private $messages;

    /**
     * Log files statuses.
     *
     * @var array
     */
    private $files = [];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * LogChecker constructor.
     *
     * @param  \Illuminate\Contracts\Config\Repository              $config
     * @param  \Ldi\LogViewer\Contracts\Utilities\Filesystem  $filesystem
     */
    public function __construct(ConfigContract $config, FilesystemContract $filesystem)
    {
        $this->setConfig($config);
        $this->setFilesystem($filesystem);
        $this->refresh();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the config instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     *
     * @return self
     */
    public function setConfig(ConfigContract $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the Filesystem instance.
     *
     * @param  \Ldi\LogViewer\Contracts\Utilities\Filesystem  $filesystem
     *
     * @return self
     */
    public function setFilesystem(FilesystemContract $filesystem): self
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Set the log handler mode.
     *
     * @param  string  $handler
     *
     * @return self
     */
    protected function setHandler($handler): self
    {
        $this->handler = strtolower($handler);

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get messages.
     *
     * @return array
     */
    public function messages(): array
    {
        $this->refresh();

        return $this->messages;
    }

    /**
     * Check if the checker passes.
     *
     * @return bool
     */
    public function passes(): bool
    {
        $this->refresh();

        return $this->status;
    }

    /**
     * Check if the checker fails.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return ! $this->passes();
    }

    /**
     * Get the requirements.
     *
     * @return array
     */
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

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Is a daily handler mode ?
     *
     * @return bool
     */
    protected function isDaily(): bool
    {
        return $this->isSameHandler(self::HANDLER_DAILY);
    }

    /**
     * Is the handler is the same as the application log handler.
     *
     * @param  string  $handler
     *
     * @return bool
     */
    private function isSameHandler(string $handler): bool
    {
        return $this->handler === $handler;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Refresh the checks.
     *
     * @return \Ldi\LogViewer\Utilities\LogChecker
     */
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

    /**
     * Check the handler mode.
     */
    private function checkHandler(): void
    {
        if ($this->isDaily()) return;

        $this->messages['handler'] = 'You should set the log handler to `daily` mode. Please check the LogViewer wiki page (Requirements) for more details.';
    }

    /**
     * Check all log files.
     */
    private function checkLogFiles(): void
    {
        foreach ($this->filesystem->all() as $path) {
            $this->checkLogFile($path);
        }
    }

    /**
     * Check a log file.
     *
     * @param  string  $path
     */
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

    /**
     * Check if it's not a single log file.
     *
     * @param  string  $file
     *
     * @return bool
     */
    private function isSingleLogFile(string $file): bool
    {
        return $file === 'laravel.log';
    }

    /**
     * Check the date of the log file.
     *
     * @param  string  $file
     * @param  string  $pattern
     *
     * @return bool
     */
    private function isInvalidLogPattern(string $file, string $pattern): bool
    {
        return ((bool) preg_match("/{$pattern}/", $file, $matches)) === false;
    }
}
