<?php

use Illuminate\Support\Facades\Route;
use Ldi\LogSpaViewer\Http\Controllers\LogViewerController;

Route::get('/', [LogViewerController::class, 'index']);

Route::get('/levels', [LogViewerController::class, 'getLevels']);

Route::delete('delete', [LogViewerController::class, 'delete']);

Route::prefix('{date}')->group(function () {
    Route::get('/', [LogViewerController::class, 'show']);
    Route::get('download', [LogViewerController::class, 'download']);
});
