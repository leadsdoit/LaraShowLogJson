<?php

use Ldi\LogViewer\Contracts;

if ( ! function_exists('log_viewer')) {
    /**
     * Get the LogViewer instance.
     *
     * @return Ldi\LogViewer\Contracts\LogViewer
     */
    function log_viewer()
    {
        return app(Contracts\LogViewer::class);
    }
}

if ( ! function_exists('log_levels')) {
    /**
     * Get the LogLevels instance.
     *
     * @return Ldi\LogViewer\Contracts\Utilities\LogLevels
     */
    function log_levels()
    {
        return app(Contracts\Utilities\LogLevels::class);
    }
}
