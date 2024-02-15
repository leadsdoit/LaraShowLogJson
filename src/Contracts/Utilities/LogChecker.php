<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Contracts\Utilities;

use Illuminate\Contracts\Config\Repository as ConfigContract;

interface LogChecker
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const HANDLER_DAILY    = 'daily';
    const HANDLER_SINGLE   = 'single';
    const HANDLER_SYSLOG   = 'syslog';
    const HANDLER_ERRORLOG = 'errorlog';

    /**
     * Set the config instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     *
     * @return self
     */
    public function setConfig(ConfigContract $config): self;

    /**
     * Set the Filesystem instance.
     *
     * @param  \Ldi\LogViewer\Contracts\Utilities\Filesystem  $filesystem
     *
     * @return self
     */
    public function setFilesystem(Filesystem $filesystem): self;

    /**
     * Get messages.
     *
     * @return array
     */
    public function messages(): array;

    /**
     * Check passes ??
     *
     * @return bool
     */
    public function passes(): bool;

    /**
     * Check fails ??
     *
     * @return bool
     */
    public function fails(): bool;

    /**
     * Get the requirements
     *
     * @return array
     */
    public function requirements(): array;
}
