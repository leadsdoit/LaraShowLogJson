<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogViewer\Http\Trait\APIResponse;
use Ldi\LogViewer\Services\LogViewerService;
use Ldi\LogViewer\Entities\{LogEntry, LogEntryCollection};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\{Collection, Str};
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewerController extends Controller
{
    use APIResponse;

    public function index(
        Request $request,
        LogViewerService $logViewerService,
//        LogViewerContract $logViewer
    ): JsonResponse
    {
        $page = $request->get('page', 1);
        $path = $request->url();
        $rows = $logViewerService->getPaginatedLogs($page, $path);
//        $perPage = config('log-viewer.per-page', 30);
//
//        $stats   = $logViewer->stats();
//
//        $data = new Collection($stats);
//        $rows = new LengthAwarePaginator(
//            $data->forPage($page, $perPage),
//            $data->count(),
//            $perPage,
//            $page,
//            compact('path')
//        );
        return $this->sendResponse($rows);
    }

    public function show(
        Request $request,
        string $date,
        LogViewerService $logViewerService,
//        LogViewerContract $logViewer
    ): JsonResponse
    {
        $level   = $request->get('level') ?? 'all';
        $query   = $request->get('query');
        $entries = $logViewerService->showLogsByDate($date, $level, $query);
//        $entries = $logViewer->entries($date, $level);
//
//        if (!empty($query)) {
//            $needles = array_map(function ($needle) {
//                return Str::lower($needle);
//            }, array_filter(explode(' ', $query)));
//
//            $entries = $entries
//                ->unless(empty($needles), function (LogEntryCollection $entries) use ($needles) {
//                    return $entries->filter(function (LogEntry $entry) use ($needles) {
//                        foreach ([$entry->header, $entry->stack, $entry->context()] as $subject) {
//                            if (Str::containsAll(Str::lower($subject), $needles))
//                                return true;
//                        }
//
//                        return false;
//                    });
//                });
//        }
//
//        $entries = $entries->paginate(config('log-viewer.per-page'));

        return $this->sendResponse([
            'entries' => $entries
        ]);

    }

    public function getLevels(LogViewerContract $logViewer): JsonResponse
    {
        $levels  = $logViewer->levelsNames();

        return $this->sendResponse([
            'levels' => array_keys($levels),
        ]);
    }

    public function download(string $date, LogViewerContract $logViewer): BinaryFileResponse
    {
        return $logViewer->download($date);
    }

    public function delete(Request $request, LogViewerContract $logViewer): JsonResponse
    {
        abort_unless($request->ajax(), 405, 'Method Not Allowed');

        $date = $request->input('date');

        return $this->sendResponse([
            'result' => $logViewer->delete($date) ? 'success' : 'error'
        ]);
    }
}
