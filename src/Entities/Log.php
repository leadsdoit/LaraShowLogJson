<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Entities;

use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use Illuminate\Support\Carbon;
use JsonSerializable;
use SplFileInfo;


class Log implements Arrayable, Jsonable, JsonSerializable
{
    public string $date;

    private string $path;

    private LogEntryCollection $entries;

    private SplFileInfo $file;

    public function __construct(string $date, string $path, string $raw)
    {
        $this->date    = $date;
        $this->path    = $path;
        $this->file    = new SplFileInfo($path);
        $this->entries = LogEntryCollection::load($raw);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function file(): SplFileInfo
    {
        return $this->file;
    }

    public function size(): string
    {
        return $this->formatSize($this->file->getSize());
    }

    public function createdAt(): Carbon
    {
        return Carbon::createFromTimestamp($this->file()->getATime());
    }

    public function updatedAt(): Carbon
    {
        return Carbon::createFromTimestamp($this->file()->getMTime());
    }

    public static function make(string $date, string $path, string $raw): self
    {
        return new self($date, $path, $raw);
    }

    public function entries(string $level = 'all'): LogEntryCollection
    {
        return $level === 'all'
            ? $this->entries
            : $this->getByLevel($level);
    }

    public function getByLevel(string $level): LogEntryCollection
    {
        return $this->entries->filterByLevel($level);
    }

    /* @using */
    public function stats(): array
    {
        return $this->entries->stats();
    }

    public function toArray(): array
    {
        return [
            'date'    => $this->date,
            'path'    => $this->path,
            'entries' => $this->entries->toArray()
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private function formatSize($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' Log.php' .$units[$pow];
    }
}
