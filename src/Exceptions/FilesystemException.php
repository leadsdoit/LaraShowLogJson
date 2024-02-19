<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Exceptions;


class FilesystemException extends LogViewerException
{
    public static function cannotDeleteLog()
    {
        return new static('Error while deleting the log.');
    }

    public static function invalidPath(string $path)
    {
        return new static("The log(s) could not be located at : $path");
    }
}
