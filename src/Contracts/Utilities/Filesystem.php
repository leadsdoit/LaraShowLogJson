<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Contracts\Utilities;

use Ldi\LogViewer\Contracts\Patternable;

interface Filesystem extends Patternable
{
    const PATTERN_PREFIX    = 'laravel-';
    const PATTERN_DATE      = '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]';
    const PATTERN_EXTENSION = '.log';

    /**
     * Get the files instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getInstance(): \Illuminate\Filesystem\Filesystem;

    /**
     * Set the log storage path.
     *
     * @param  string  $storagePath
     *
     * @return $this
     */
    public function setPath(string $storagePath): self;

    /**
     * Set the log date pattern.
     *
     * @param  string  $datePattern
     *
     * @return $this
     */
    public function setDatePattern(string $datePattern): self;

    /**
     * Set the log prefix pattern.
     *
     * @param  string  $prefixPattern
     *
     * @return $this
     */
    public function setPrefixPattern(string $prefixPattern): self;

    /**
     * Set the log extension.
     *
     * @param  string  $extension
     *
     * @return $this
     */
    public function setExtension(string $extension): self;

    /**
     * Get all log files.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get all valid log files.
     *
     * @return array
     */
    public function logs(): array;

    /**
     * List the log files (Only dates).
     *
     * @param  bool  $withPaths
     *
     * @return array
     */
    public function dates(bool $withPaths = false): array;

    /**
     * Read the log.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws \Ldi\LogViewer\Exceptions\FilesystemException
     */
    public function read(string $date): string;

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
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     */
    public function path(string $date): string;
}
