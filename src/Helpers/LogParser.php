<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Helpers;

use Ldi\LogSpaViewer\Utilities\LogLevels;
use Illuminate\Support\Str;

class LogParser
{

    const REGEX_DATE_PATTERN     = '\d{4}(-\d{2}){2}';
    const REGEX_TIME_PATTERN     = '\d{2}(:\d{2}){2}';
    const REGEX_DATETIME_PATTERN = self::REGEX_DATE_PATTERN.' '.self::REGEX_TIME_PATTERN;


    protected static array $parsed = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse file content.
     *
     * @param  string  $raw
     *
     * @return array
     */
    public static function parse(string $raw): array
    {
        static::$parsed          = [];
        [$headings, $data] = static::parseRawData($raw);

        // @codeCoverageIgnoreStart
        if ( ! is_array($headings)) {
            return static::$parsed;
        }
        // @codeCoverageIgnoreEnd

        foreach ($headings as $heading) {
            for ($i = 0, $j = count($heading); $i < $j; $i++) {
                static::populateEntries($heading, $data, $i);
            }
        };

        unset($headings, $data);

        return array_reverse(static::$parsed);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Extract the date.
     *
     * @param  string  $string
     *
     * @return string
     */
    public static function extractDate(string $string): string
    {
        return preg_replace('/.*('.self::REGEX_DATE_PATTERN.').*/', '$1', $string);
    }

    /**
     * Parse raw data.
     *
     * @param  string  $raw
     *
     * @return array
     */
    private static function parseRawData(string $raw): array
    {
        $pattern = '/\['.self::REGEX_DATETIME_PATTERN.'\].*/';
        preg_match_all($pattern, $raw, $headings);
        $data    = preg_split($pattern, $raw);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        return [$headings, $data];
    }

    /**
     * Populate entries.
     *
     * @param  array  $heading
     * @param  array  $data
     * @param  int  $key
     */
    private static function populateEntries(array $heading, array $data, int $key): void
    {
        foreach (LogLevels::all() as $level) {
            if (static::hasLogLevel($heading[$key], $level)) {
                static::$parsed[] = [
                    'level'  => $level,
                    'header' => $heading[$key],
                    'stack'  => $data[$key]
                ];
            }
        }
    }

    /**
     * Check if header has a log level.
     *
     * @param  string  $heading
     * @param  string  $level
     *
     * @return bool
     */
    private static function hasLogLevel(string $heading, string $level): bool
    {
        return Str::contains($heading, strtoupper(".{$level}:"));
    }
}
