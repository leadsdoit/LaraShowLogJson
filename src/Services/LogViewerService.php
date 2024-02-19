<?php

namespace Ldi\LogViewer\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
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

    public function showLogsByDate(string $date, string $level, ?string $query): LengthAwarePaginator
    {
        /* @var LogViewerContract $logViewer */
        $logViewer = resolve(LogViewerContract::class);

        $entries = $logViewer->entries($date, $level);

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

        $entries = $entries->paginate(config('log-viewer.per-page'));

        return $entries;

    }

}
