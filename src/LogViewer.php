<?php

declare(strict_types=1);

namespace Ldi\LogViewer;

use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Contracts\Utilities\Factory as FactoryContract;
use Ldi\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogViewer\Entities\LogCollection;
use Ldi\LogViewer\Entities\LogEntryCollection;
use Ldi\LogViewer\Utilities\Factory;
use Ldi\LogViewer\Utilities\LogLevels;
use Ldi\LogViewer\Entities\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewer implements LogViewerContract
{
    /**
     * The factory instance.
     *
     * @var \Ldi\LogViewer\Contracts\Utilities\Factory
     */
    protected Factory $factory;

    /**
     * The filesystem instance.
     *
     * @var \Ldi\LogViewer\Contracts\Utilities\Filesystem
     */
    protected FilesystemContract $filesystem;

    /**
     * The log levels instance.
     *
     * @var \Ldi\LogViewer\Contracts\Utilities\LogLevels
     */
    protected LogLevels $levels;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new instance.
     *
     * @param \Ldi\LogViewer\Contracts\Utilities\Factory $factory
     * @param \Ldi\LogViewer\Contracts\Utilities\Filesystem $filesystem
     * @param \Ldi\LogViewer\Contracts\Utilities\LogLevels $levels
     */
    public function __construct(
        FactoryContract $factory,
        FilesystemContract $filesystem,
        LogLevelsContract $levels
    ) {
        $this->factory = $factory;
        $this->filesystem = $filesystem;
        $this->levels = $levels;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the log levels.
     *
     * @param bool $flip
     *
     * @return array
     */
    public function levels(bool $flip = false): array
    {
        return $this->levels->lists($flip);
    }

    /**
     * Get the translated log levels.
     *
     * @param string|null $locale
     *
     * @return array
     */
    public function levelsNames(?string $locale = null): array
    {
        return $this->levels->names($locale);
    }

    /**
     * Set the log storage path.
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->factory->setPath($path);

        return $this;
    }

    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->factory->getPattern();
    }

    /**
     * Set the log pattern.
     *
     * @param string $date
     * @param string $prefix
     * @param string $extension
     *
     * @return self
     */
    public function setPattern(
        string $prefix = FilesystemContract::PATTERN_PREFIX,
        string $date = FilesystemContract::PATTERN_DATE,
        string $extension = FilesystemContract::PATTERN_EXTENSION
    ): self {
        $this->factory->setPattern($prefix, $date, $extension);

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all logs.
     *
     * @return \Ldi\LogViewer\Entities\LogCollection
     */
    public function all(): LogCollection
    {
        return $this->factory->all();
    }

    /**
     * Paginate all logs.
     *
     * @param int $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        return $this->factory->paginate($perPage);
    }

    /**
     * Get a log.
     *
     * @param string $date
     *
     * @return \Ldi\LogViewer\Entities\Log
     */
    public function get(string $date): Log
    {
        return $this->factory->log($date);
    }

    /**
     * Get the log entries.
     *
     * @param string $date
     * @param string $level
     *
     * @return \Ldi\LogViewer\Entities\LogEntryCollection
     */
    public function entries(string $date, string $level = 'all'): LogEntryCollection
    {
        return $this->factory->entries($date, $level);
    }

    /**
     * Download a log file.
     *
     * @param string $date
     * @param string|null $filename
     * @param array $headers
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $date, ?string $filename = null, array $headers = []): BinaryFileResponse
    {
        if (is_null($filename)) {
            $filename = sprintf(
                "%s{$date}.%s",
                config('log-viewer.download.prefix', 'laravel-'),
                config('log-viewer.download.extension', 'log')
            );
        }

        $path = $this->filesystem->path($date);

        return response()->download($path, $filename, $headers);
    }

    /**
     * Get logs statistics.
     * @using
     * @return array
     */
    public function stats(): array
    {
        return $this->factory->stats();
    }

    /**
     * Delete the log.
     *
     * @param string $date
     *
     * @return bool
     */
    public function delete(string $date): bool
    {
        return $this->filesystem->delete($date);
    }

    /**
     * Clear the log files.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->filesystem->clear();
    }

    /**
     * Get all valid log files.
     *
     * @return array
     */
    public function files(): array
    {
        return $this->filesystem->logs();
    }

    /**
     * List the log files (only dates).
     *
     * @return array
     */
    public function dates(): array
    {
        return $this->factory->dates();
    }

    /**
     * Get logs count.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->factory->count();
    }

    /**
     * Get entries total from all logs.
     *
     * @param string $level
     *
     * @return int
     */
    public function total(string $level = 'all'): int
    {
        return $this->factory->total($level);
    }

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->factory->isEmpty();
    }

}
