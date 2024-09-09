<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Utilities;

use Ldi\LogSpaViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogSpaViewer\Exceptions\FilesystemException;
use Ldi\LogSpaViewer\Helpers\LogParser;
use Exception;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;

class Filesystem implements FilesystemContract
{

    protected IlluminateFilesystem $filesystem;

    protected string $storagePath;

    protected string $prefixPattern;

    protected string $datePattern;

    protected string $extension;

    public function __construct(IlluminateFilesystem $files, string $storagePath)
    {
        $this->filesystem  = $files;
        $this->setPath($storagePath);

        $pattern = config('log-spa-viewer.pattern');
        $this->setPattern($pattern['prefix'], $pattern['date'], $pattern['extension']);
    }

    public function getInstance(): IlluminateFilesystem
    {
        return $this->filesystem;
    }

    public function setPath(string $storagePath): self
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    public function getPattern(): string
    {
        return $this->prefixPattern.$this->datePattern.$this->extension;
    }

    public function setPattern(string $prefix, string $date, string $extension): self
    {
        $this->setPrefixPattern($prefix);
        $this->setDatePattern($date);
        $this->setExtension($extension);

        return $this;
    }

    public function setDatePattern(string $datePattern): self
    {
        $this->datePattern = $datePattern;

        return $this;
    }

    public function setPrefixPattern(string $prefixPattern): self
    {
        $this->prefixPattern = $prefixPattern;

        return $this;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function all(): array
    {
        return $this->getFiles('*'.$this->extension);
    }

    public function logs(): array
    {
        return $this->getFiles($this->getPattern());
    }

    public function dates(bool $withPaths = false): array
    {
        $files = array_reverse($this->logs());
        $dates = $this->extractDates($files);

        if ($withPaths) {
            $dates = array_combine($dates, $files); // [date => file]
        }

        return $dates;
    }

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

    public function delete(string $date): bool
    {
        $path = $this->getLogPath($date);

        throw_unless($this->filesystem->delete($path), FilesystemException::cannotDeleteLog());

        return true;
    }

    public function clear(): bool
    {
        return $this->filesystem->delete($this->logs());
    }

    public function path(string $date): string
    {
        return $this->getLogPath($date);
    }

    private function getFiles(string $pattern): array
    {
        $files = $this->filesystem->glob(
            $this->storagePath.DIRECTORY_SEPARATOR.$pattern, defined('GLOB_BRACE') ? GLOB_BRACE : 0
        );

        return array_filter(array_map('realpath', $files));
    }

    private function getLogPath(string $date): string
    {
        $path = $this->storagePath.DIRECTORY_SEPARATOR.$this->prefixPattern.$date.$this->extension;

        if ( ! $this->filesystem->exists($path)) {
            throw FilesystemException::invalidPath($path);
        }

        return realpath($path);
    }

    private function extractDates(array $files): array
    {
        return array_map(function ($file) {
            return LogParser::extractDate(basename($file));
        }, $files);
    }
}
