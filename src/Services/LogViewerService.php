<?php

namespace Ldi\LogViewer\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogViewer\Entities\Log;
use Ldi\LogViewer\Entities\LogEntry;
use Ldi\LogViewer\Entities\LogEntryCollection;

class LogViewerService
{
    public function getPaginatedLogs(int $page, string $path): LengthAwarePaginator
    {
        $perPage = config('log-viewer.per-page', 30);

        /* @var LogViewerContract $logViewer */
        $logViewer = resolve(LogViewerContract::class);

        $stats   = $logViewer->stats();

        $data = new Collection($stats);
        $rows = new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            $data->count(),
            $perPage,
            $page,
            compact('path')
        );

        return $rows;
    }

    public function getLogInfoByDate(string $date): mixed
    {
        $logViewer = resolve(LogViewerContract::class);

        return $logViewer->get($date);
    }

    public function getLevelStats(string $date): ?array
    {
        $logViewer = resolve(LogViewerContract::class);

        $allStats = $logViewer->stats();

        return $allStats[$date] ?? null;
    }

    public function showLogsByDate(Log $log, string $level, ?string $query): LengthAwarePaginator
    {
        $entries = $log->entries($level);

        if (!empty($query)) {
            $needles = array_map(function ($needle) {
                return Str::lower($needle);
            }, array_filter(explode(' ', $query)));

            $entries = $entries
                ->unless(empty($needles), function (LogEntryCollection $entries) use ($needles) {
                    return $entries->filter(function (LogEntry $entry) use ($needles) {
                        foreach ([$entry->header, $entry->stack, $entry->context()] as $subject) {
                            if (Str::containsAll(Str::lower($subject), $needles))
                                return true;
                        }
                        return false;
                    });
                });
        }

        return $entries->paginate(config('log-viewer.per-page'));

    }


    public function paginateLogInfo(?Log $log, string $level, ?string $query): array
    {
        if (!empty($log)) {
            $entries = $this->showLogsByDate($log, $level, $query);

            $result = [
                'level_statistics' => $log->level_statistics ?? [],
                'created_at' => $log->createdAt()->format('Y-m-d H:i:s') ?? null,
                'updated_at' => $log->updatedAt()->format('Y-m-d H:i:s') ?? null,
                'size' => $log->size() ?? null,
            ];

            $result = array_merge($result, $log->toArray());
            $result['entries'] = $entries;
        }

        return $result ?? [];
    }

}
