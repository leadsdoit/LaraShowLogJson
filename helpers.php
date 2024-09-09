<?php

use Ldi\LogSpaViewer\Contracts;

if ( ! function_exists('log_spa_viewer')) {
    /**
     * Get the LogViewer instance.
     *
     * @return Ldi\LogSpaViewer\Contracts\LogViewer
     */
    function log_spa_viewer()
    {
        return app(Contracts\LogViewer::class);
    }
}

if ( ! function_exists('log_spa_levels')) {
    /**
     * Get the LogLevels instance.
     *
     * @return Ldi\LogSpaViewer\Contracts\Utilities\LogLevels
     */
    function log_spa_levels()
    {
        return app(Contracts\Utilities\LogLevels::class);
    }
}
