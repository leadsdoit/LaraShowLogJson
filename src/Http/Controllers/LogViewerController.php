<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Http\Controllers;

use Ldi\LogViewer\Contracts\LogViewer as LogViewerContract;
use Ldi\LogViewer\Http\Trait\APIResponse;
use Ldi\LogViewer\Entities\{LogEntry, LogEntryCollection};
use Ldi\LogViewer\Exceptions\LogNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\{Arr, Collection, Str};
/**
 * Class     LogViewerController
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LogViewerController extends Controller
{
    use APIResponse;

    /**
     * The log viewer instance
     *
     * @var \Ldi\LogViewer\Contracts\LogViewer
     */
    protected $logViewer;

    /** @var int */
    protected $perPage = 30;

    /**
     * Show the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, LogViewerContract $logViewer)
    {
        $stats   = $logViewer->stats();
        $rows    = $this->paginate($stats, $request);
        return $this->sendResponse($rows);
    }

    /**
     * Show the log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $date
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $date, LogViewerContract $logViewer)
    {
        $level   = $request->get('level') ?? 'all';
        $query   = $request->get('query');
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

        $entries = $entries->paginate(config('log-viewer.per-page', $this->perPage));

        return $this->sendResponse([
            'entries' => $entries
        ]);

    }

    public function getLevels(LogViewerContract $logViewer)
    {
        $levels  = $logViewer->levelsNames();

        return $this->sendResponse([
            'levels' => array_keys($levels),
        ]);
    }

    /**
     * Download the log
     *
     * @param  string  $date
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $date, LogViewerContract $logViewer)
    {
        return $logViewer->download($date);
    }

    /**
     * Delete a log.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, LogViewerContract $logViewer)
    {
        abort_unless($request->ajax(), 405, 'Method Not Allowed');

        $date = $request->input('date');

        return $this->sendResponse([
            'result' => $logViewer->delete($date) ? 'success' : 'error'
        ]);
    }

    /**
     * Paginate logs.
     *
     * @param  array                     $data
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginate(array $data, Request $request)
    {
        $data = new Collection($data);
        $page = $request->get('page', 1);
        $path = $request->url();

        return new LengthAwarePaginator(
            $data->forPage($page, config('log-viewer.per-page', $this->perPage)),
            $data->count(),
            config('log-viewer.per-page', $this->perPage),
            $page,
            compact('path')
        );
    }
}
