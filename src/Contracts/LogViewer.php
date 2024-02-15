<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Contracts;

use Ldi\LogViewer\Entities\Log;
use Ldi\LogViewer\Entities\LogEntryCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Interface  LogViewer
 *
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface LogViewer extends Patternable
{
    /**
     * Get the log levels.
     *
     * @param  bool|false  $flip
     *
     * @return array
     */
    public function levels(bool $flip = false): array;

    /**
     * Get the translated log levels.
     *
     * @param  string|null  $locale
     *
     * @return array
     */
    public function levelsNames(?string $locale = null): array;

    /**
     * Set the log storage path.
     *
     * @param  string  $path
     *
     * @return self
     */
    public function setPath(string $path): self;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all logs.
     *
     * @return \Ldi\LogViewer\Entities\LogCollection|\Ldi\LogViewer\Entities\Log[]
     */
    public function all();

    /**
     * Paginate all logs.
     *
     * @param  int  $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 30): LengthAwarePaginator;

    /**
     * Get a log.
     *
     * @param  string  $date
     *
     * @return \Ldi\LogViewer\Entities\Log
     */
    public function get(string $date): Log;

    /**
     * Get the log entries.
     *
     * @param  string  $date
     * @param  string  $level
     *
     * @return \Ldi\LogViewer\Entities\LogEntryCollection
     */
    public function entries(string $date, string $level = 'all'): LogEntryCollection;

    /**
     * Download a log file.
     *
     * @param  string       $date
     * @param  string|null  $filename
     * @param  array        $headers
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $date, ?string $filename = null, array $headers = []): BinaryFileResponse;

    /**
     * Get logs statistics.
     * @using
     * @return array
     */
    public function stats(): array;

    /**
     * Delete the log.
     *
     * @param  string  $date
     *
     * @return bool
     *
     * @throws \Ldi\LogViewer\Exceptions\FilesystemException
     */
    public function delete(string $date): bool;

    /**
     * Clear the log files.
     *
     * @return bool
     */
    public function clear(): bool;

    /**
     * List the log files.
     *
     * @return array
     */
    public function files(): array;

    /**
     * List the log files (only dates).
     *
     * @return array
     */
    public function dates(): array;

    /**
     * Get logs count.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Get entries total from all logs.
     *
     * @param  string  $level
     *
     * @return int
     */
    public function total(string $level = 'all'): int;

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
