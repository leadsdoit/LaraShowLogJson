<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer;

use Ldi\LogSpaViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogSpaViewer\Contracts\Utilities\Factory as FactoryContract;
use Ldi\LogSpaViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Ldi\LogSpaViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogSpaViewer\Entities\LogCollection;
use Ldi\LogSpaViewer\Entities\LogEntryCollection;
use Ldi\LogSpaViewer\Utilities\Factory;
use Ldi\LogSpaViewer\Utilities\LogLevels;
use Ldi\LogSpaViewer\Entities\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogSpaViewer implements LogViewerContract
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

    public function setPattern(string $prefix, string $date, string $extension): self
    {
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
                config('log-spa-viewer.download.prefix', 'laravel-'),
                config('log-spa-viewer.download.extension', 'log')
            );
        }

        $path = $this->filesystem->path($date);

        return response()->download($path, $filename, $headers);
    }

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
