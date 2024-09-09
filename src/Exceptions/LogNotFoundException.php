<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Exceptions;

class LogNotFoundException extends LogViewerException
{
    public static function make(string $date): static
    {
        return new static("Log not found in this date [{$date}]");
    }
}
