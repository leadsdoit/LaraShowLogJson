<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Contracts\Utilities;

use Ldi\LogSpaViewer\Contracts\Patternable;
use Ldi\LogSpaViewer\Entities\LogCollection;
use Ldi\LogSpaViewer\Entities\LogEntryCollection;
use Ldi\LogSpaViewer\Entities\Log;
use Illuminate\Pagination\LengthAwarePaginator;

interface Factory extends Patternable
{

    /**
     * Get the filesystem instance.
     */
    public function getFilesystem(): Filesystem;

    /**
     * Set the filesystem instance.
     */
    public function setFilesystem(Filesystem $filesystem): self;

    /**
     * Get the log levels instance.
     */
    public function getLevels(): LogLevels;

    /**
     * Set the log levels instance.
     */
    public function setLevels(LogLevels $levels): self;

    /**
     * Set the log storage path.
     */
    public function setPath(string $storagePath): self;

    /**
     * Get all logs.
     */
    public function logs(): LogCollection;

    /**
     * Get all logs (alias).
     */
    public function all(): LogCollection;

    /**
     * Paginate all logs.
     */
    public function paginate(int $perPage = 30): LengthAwarePaginator;

    /**
     * Get a log by date.
     */
    public function log(string $date): Log;

    /**
     * Get a log by date (alias).
     */
    public function get(string $date): Log;

    /**
     * Get log entries.
     */
    public function entries(string $date, string $level = 'all'): LogEntryCollection;

    /**
     * List the log files (dates).
     */
    public function dates(): array;

    /**
     * Get logs count.
     */
    public function count(): int;

    /**
     * Get total log entries.
     */
    public function total(string $level = 'all'): int;

    /**
     * Get logs statistics.
     */
    public function stats(): array;

    /**
     * Determine if the log folder is empty or not.
     */
    public function isEmpty(): bool;
}
