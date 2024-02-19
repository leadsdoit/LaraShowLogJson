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
    protected Factory $factory;

    protected FilesystemContract $filesystem;

    protected LogLevels $levels;

    public function __construct(
        FactoryContract $factory,
        FilesystemContract $filesystem,
        LogLevelsContract $levels
    ) {
        $this->factory = $factory;
        $this->filesystem = $filesystem;
        $this->levels = $levels;
    }

    public function levels(bool $flip = false): array
    {
        return $this->levels->lists($flip);
    }

    public function levelsNames(?string $locale = null): array
    {
        return $this->levels->names($locale);
    }

    public function setPath(string $path): self
    {
        $this->factory->setPath($path);

        return $this;
    }

    public function getPattern(): string
    {
        return $this->factory->getPattern();
    }

    public function setPattern(
        string $prefix = FilesystemContract::PATTERN_PREFIX,
        string $date = FilesystemContract::PATTERN_DATE,
        string $extension = FilesystemContract::PATTERN_EXTENSION
    ): self {
        $this->factory->setPattern($prefix, $date, $extension);

        return $this;
    }

    public function all(): LogCollection
    {
        return $this->factory->all();
    }

    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        return $this->factory->paginate($perPage);
    }

    public function get(string $date): Log
    {
        return $this->factory->log($date);
    }

    public function entries(string $date, string $level = 'all'): LogEntryCollection
    {
        return $this->factory->entries($date, $level);
    }

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

    /* @using */
    public function stats(): array
    {
        return $this->factory->stats();
    }

    public function delete(string $date): bool
    {
        return $this->filesystem->delete($date);
    }

    public function clear(): bool
    {
        return $this->filesystem->clear();
    }

    public function files(): array
    {
        return $this->filesystem->logs();
    }

    public function dates(): array
    {
        return $this->factory->dates();
    }

    public function count(): int
    {
        return $this->factory->count();
    }

    public function total(string $level = 'all'): int
    {
        return $this->factory->total($level);
    }

    public function isEmpty(): bool
    {
        return $this->factory->isEmpty();
    }

}
