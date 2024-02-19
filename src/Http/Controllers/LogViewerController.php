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

    public function index(Request $request, LogViewerService $logViewerService): JsonResponse
    {
        $page = $request->get('page', 1);
        $path = $request->url();
        $rows = $logViewerService->getPaginatedLogs($page, $path);

        return $this->sendResponse($rows);
    }

    public function show(Request $request, string $date, LogViewerService $logViewerService): JsonResponse
    {
        $level   = $request->get('level') ?? 'all';
        $query   = $request->get('query');
        $entries = $logViewerService->showLogsByDate($date, $level, $query);

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
