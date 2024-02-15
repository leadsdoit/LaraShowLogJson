<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Utilities;

use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Exceptions\FilesystemException;
use Ldi\LogViewer\Helpers\LogParser;
use Exception;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;

class Filesystem implements FilesystemContract
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected \Illuminate\Filesystem\Filesystem $filesystem;

    /**
     * The base storage path.
     *
     * @var string
     */
    protected string $storagePath;

    /**
     * The log files prefix pattern.
     *
     * @var string
     */
    protected string $prefixPattern;

    /**
     * The log files date pattern.
     *
     * @var string
     */
    protected string $datePattern;

    /**
     * The log files extension.
     *
     * @var string
     */
    protected string $extension;


    /**
     * Filesystem constructor.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string                             $storagePath
     */
    public function __construct(IlluminateFilesystem $files, string $storagePath)
    {
        $this->filesystem  = $files;
        $this->setPath($storagePath);
        $this->setPattern();
    }

    /**
     * Get the files instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getInstance(): \Illuminate\Filesystem\Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Set the log storage path.
     *
     * @param  string  $storagePath
     *
     * @return $this
     */
    public function setPath(string $storagePath): self
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->prefixPattern.$this->datePattern.$this->extension;
    }

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     *
     * @return $this
     */
    public function setPattern(
       string $prefix    = self::PATTERN_PREFIX,
       string $date      = self::PATTERN_DATE,
       string $extension = self::PATTERN_EXTENSION
    ): self {
        $this->setPrefixPattern($prefix);
        $this->setDatePattern($date);
        $this->setExtension($extension);

        return $this;
    }

    /**
     * Set the log date pattern.
     *
     * @param  string  $datePattern
     *
     * @return $this
     */
    public function setDatePattern(string $datePattern): self
    {
        $this->datePattern = $datePattern;

        return $this;
    }

    /**
     * Set the log prefix pattern.
     *
     * @param  string  $prefixPattern
     *
     * @return $this
     */
    public function setPrefixPattern(string $prefixPattern): self
    {
        $this->prefixPattern = $prefixPattern;

        return $this;
    }

    /**
     * Set the log extension.
     *
     * @param  string  $extension
     *
     * @return $this
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all log files.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->getFiles('*'.$this->extension);
    }

    /**
     * Get all valid log files.
     *
     * @return array
     */
    public function logs(): array
    {
        return $this->getFiles($this->getPattern());
    }

    /**
     * List the log files (Only dates).
     *
     * @param  bool  $withPaths
     *
     * @return array
     */
    public function dates(bool $withPaths = false): array
    {
        $files = array_reverse($this->logs());
        $dates = $this->extractDates($files);

        if ($withPaths) {
            $dates = array_combine($dates, $files); // [date => file]
        }

        return $dates;
    }

    /**
     * Read the log.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws \Ldi\LogViewer\Exceptions\FilesystemException
     */
    public function read(string $date): string
    {
        try {
            $log = $this->filesystem->get(
                $this->getLogPath($date)
            );
        }
        catch (Exception $e) {
            throw new FilesystemException($e->getMessage());
        }

        return $log;
    }

    /**
     * Delete the log.
     *
     * @param  string  $date
     *
     * @return bool
     *
     * @throws \Ldi\LogViewer\Exceptions\FilesystemException
     */
    public function delete(string $date): bool
    {
        $path = $this->getLogPath($date);

        throw_unless($this->filesystem->delete($path), FilesystemException::cannotDeleteLog());

        return true;
    }

    /**
     * Clear the log files.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->filesystem->delete($this->logs());
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     */
    public function path(string $date): string
    {
        return $this->getLogPath($date);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all files.
     *
     * @param  string  $pattern
     *
     * @return array
     */
    private function getFiles(string $pattern): array
    {
        $files = $this->filesystem->glob(
            $this->storagePath.DIRECTORY_SEPARATOR.$pattern, defined('GLOB_BRACE') ? GLOB_BRACE : 0
        );

        return array_filter(array_map('realpath', $files));
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws \Ldi\LogViewer\Exceptions\FilesystemException
     */
    private function getLogPath(string $date): string
    {
        $path = $this->storagePath.DIRECTORY_SEPARATOR.$this->prefixPattern.$date.$this->extension;

        if ( ! $this->filesystem->exists($path)) {
            throw FilesystemException::invalidPath($path);
        }

        return realpath($path);
    }

    /**
     * Extract dates from files.
     *
     * @param  array  $files
     *
     * @return array
     */
    private function extractDates(array $files): array
    {
        return array_map(function ($file) {
            return LogParser::extractDate(basename($file));
        }, $files);
    }
}
