<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Utilities;

use Ldi\LogViewer\Contracts\Utilities\Factory as FactoryContract;
use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Ldi\LogViewer\Entities\LogCollection;
use Ldi\LogViewer\Entities\Log;
use Ldi\LogViewer\Entities\LogEntryCollection;
use Ldi\LogViewer\Exceptions\LogNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class Factory implements FactoryContract
{
    /**
     * The filesystem instance.
     */
    protected Filesystem $filesystem;

    /**
     * The log levels instance.
     */
    private LogLevels $levels;

    /**
     * Create a new instance.
     */
    public function __construct(FilesystemContract $filesystem, LogLevelsContract $levels)
    {
        $this->setFilesystem($filesystem);
        $this->setLevels($levels);
    }

    /**
     * Get the filesystem instance.
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Set the filesystem instance.
     */
    public function setFilesystem(FilesystemContract $filesystem): self
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Get the log levels instance.
     */
    public function getLevels(): LogLevels
    {
        return $this->levels;
    }

    /**
     * Set the log levels instance.
     */
    public function setLevels(LogLevelsContract $levels): self
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Set the log storage path.
     */
    public function setPath(string $storagePath): self
    {
        $this->filesystem->setPath($storagePath);

        return $this;
    }

    /**
     * Get the log pattern.
     */
    public function getPattern(): string
    {
        return $this->filesystem->getPattern();
    }

    /**
     * Set the log pattern.
     */
    public function setPattern(
        string $prefix    = FilesystemContract::PATTERN_PREFIX,
        string $date      = FilesystemContract::PATTERN_DATE,
        string $extension = FilesystemContract::PATTERN_EXTENSION
    ) : self
    {
        $this->filesystem->setPattern($prefix, $date, $extension);

        return $this;
    }

    /**
     * Get all logs.
     */
    public function logs(): LogCollection
    {
        return (new LogCollection)->setFilesystem($this->filesystem);
    }

    /**
     * Get all logs (alias).
     */
    public function all(): LogCollection
    {
        return $this->logs();
    }

    /**
     * Paginate all logs.
     */
    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        return $this->logs()->paginate($perPage);
    }

    /**
     * Get a log by date.
     */
    public function log(string $date): Log
    {
        $dates = $this->filesystem->dates(true);
        if (!isset($dates[$date])) {
            throw new LogNotFoundException("Log not found in this date [$date]");
        }

        return new Log($date, $dates[$date], $this->filesystem->read($date));
    }

    /**
     * Get a log by date (alias).
     */
    public function get(string $date): Log
    {
        return $this->log($date);
    }

    /**
     * Get log entries.
     */
    public function entries(string $date, string $level = 'all'): LogEntryCollection
    {
        return $this->log($date)->entries($level);
    }

    /**
     * Get logs statistics.
     */
    public function stats(): array
    {
        return $this->logs()->stats();
    }

    /**
     * List the log files (dates).
     */
    public function dates(): array
    {
        return $this->filesystem->dates();
    }

    /**
     * Get logs count.
     */
    public function count(): int
    {
        return $this->logs()->count();
    }

    /**
     * Get total log entries.
     */
    public function total(string $level = 'all'): int
    {
        return $this->logs()->total($level);
    }

    /**
     * Determine if the log folder is empty or not.
     */
    public function isEmpty(): bool
    {
        return $this->logs()->isEmpty();
    }
}
