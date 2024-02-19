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

    protected Filesystem $filesystem;
    private LogLevels $levels;

    public function __construct(FilesystemContract $filesystem, LogLevelsContract $levels)
    {
        $this->setFilesystem($filesystem);
        $this->setLevels($levels);
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function setFilesystem(FilesystemContract $filesystem): self
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    public function getLevels(): LogLevels
    {
        return $this->levels;
    }

    public function setLevels(LogLevelsContract $levels): self
    {
        $this->levels = $levels;

        return $this;
    }

    public function setPath(string $storagePath): self
    {
        $this->filesystem->setPath($storagePath);

        return $this;
    }

    public function getPattern(): string
    {
        return $this->filesystem->getPattern();
    }

    public function setPattern(string $prefix, string $date, string $extension): self
    {
        $this->filesystem->setPattern($prefix, $date, $extension);

        return $this;
    }

    public function logs(): LogCollection
    {
        return (new LogCollection)->setFilesystem($this->filesystem);
    }

    public function all(): LogCollection
    {
        return $this->logs();
    }

    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        return $this->logs()->paginate($perPage);
    }

    public function log(string $date): Log
    {
        $dates = $this->filesystem->dates(true);
        if (!isset($dates[$date])) {
            throw new LogNotFoundException("Log not found in this date [$date]");
        }

        return new Log($date, $dates[$date], $this->filesystem->read($date));
    }

    public function get(string $date): Log
    {
        return $this->log($date);
    }

    public function entries(string $date, string $level = 'all'): LogEntryCollection
    {
        return $this->log($date)->entries($level);
    }

    public function stats(): array
    {
        return $this->logs()->stats();
    }

    public function dates(): array
    {
        return $this->filesystem->dates();
    }

    public function count(): int
    {
        return $this->logs()->count();
    }

    public function total(string $level = 'all'): int
    {
        return $this->logs()->total($level);
    }

    public function isEmpty(): bool
    {
        return $this->logs()->isEmpty();
    }
}
