<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Ldi\LogSpaViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogSpaViewer\Http\Trait\APIResponse;
use Ldi\LogSpaViewer\Services\LogSpaViewerService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewerController extends Controller
{
    use APIResponse;

    public function index(Request $request, LogSpaViewerService $logViewerService): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = config('log-spa-viewer.per-page', 30);
        $path = $request->url();

        $rows = $logViewerService->paginate($page, $path, $perPage);

        return $this->sendResponse($rows);
    }

    public function getLevels(LogViewerContract $logViewer): JsonResponse
    {
        $levels = $logViewer->levelsNames();

        return $this->sendResponse([
            'levels' => array_keys($levels),
        ]);
    }

    public function delete(Request $request, LogViewerContract $logViewer): JsonResponse
    {
        $date = $request->get('date');

        return $this->sendResponse([
            'result' => $logViewer->delete($date) ? 'success' : 'error'
        ]);
    }

    public function show(Request $request, string $date, LogSpaViewerService $logViewerService): JsonResponse
    {
        $level = $request->get('level') ?? 'all';
        $query = $request->get('query');

        $log = $logViewerService->getLogInfoByDate($date);
        $log->level_statistics = $logViewerService->getLevelStats($date);
        $result = $logViewerService->paginateLogInfo($log, $level, $query);

        return $this->sendResponse($result);

    }

    public function download(string $date, LogViewerContract $logViewer): BinaryFileResponse
    {
        return $logViewer->download($date);
    }

}
