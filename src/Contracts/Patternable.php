<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Contracts;

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
    public function setPattern(string $prefix, string $date, string $extension): self;
}
