<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Entities;

use Ldi\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Ldi\LogViewer\Exceptions\LogNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

class LogCollection extends LazyCollection
{
    private FilesystemContract $filesystem;

    public function __construct(mixed $source = null)
    {
        $this->setFilesystem(app(FilesystemContract::class));

        if (is_null($source))
            $source = function () {
                foreach($this->filesystem->dates(true) as $date => $path) {
                    yield $date => Log::make($date, $path, $this->filesystem->read($date));
                }
            };

        parent::__construct($source);
    }

    public function setFilesystem(FilesystemContract $filesystem): LogCollection
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Get a log.
     *
     * @param  string      $date
     * @param  mixed|null  $default
     *
     * @return Log
     *
     * @throws \Ldi\LogViewer\Exceptions\LogNotFoundException
     */
    public function get($key, $default = null): Log
    {
        if ( ! $this->has($key)) {
            throw LogNotFoundException::make($key ?? '');
        }

        return parent::get($key, $default);
    }

    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $path = request()->url();

        return new LengthAwarePaginator(
            $this->forPage($page, $perPage),
            $this->count(),
            $perPage,
            $page,
            compact('path')
        );
    }

    public function log(string $date): Log
    {
        return $this->get($date);
    }

    public function entries(string $date, string $level = 'all'): LogEntryCollection
    {
        return $this->get($date)->entries($level);
    }

    /* @using */
    public function stats(): array
    {
        $stats = [];

        foreach ($this->all() as $date => $log) {
            /** @var Log $log */
            $stats[$date] = $log->stats();
        }

        return $stats;
    }

    public function dates(): array
    {
        return $this->keys()->toArray();
    }

    public function total(string $level = 'all'):  int
    {
        return (int) $this->sum(function (Log $log) use ($level) {
            return $log->entries($level)->count();
        });
    }

}
