<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Contracts;

use Ldi\LogViewer\Contracts\Utilities\Filesystem;

interface Patternable
{
    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern(): string;

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     *
     * @return self
     */
    public function setPattern(
        string $prefix    = Filesystem::PATTERN_PREFIX,
        string $date      = Filesystem::PATTERN_DATE,
        string $extension = Filesystem::PATTERN_EXTENSION
    ): self;
}
