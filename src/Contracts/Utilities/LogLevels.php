<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Contracts\Utilities;

use Illuminate\Translation\Translator;

interface LogLevels
{
    /**
     * Set the Translator instance.
     *
     * @param  \Illuminate\Translation\Translator  $translator
     *
     * @return self
     */
    public function setTranslator(Translator $translator): self;

    /**
     * Get the selected locale.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Set the selected locale.
     *
     * @param  string  $locale
     *
     * @return self
     */
    public function setLocale(string $locale): self;

    /**
     * Get the log levels.
     *
     * @param  bool  $flip
     *
     * @return array
     */
    public function lists(bool $flip = false): array;

    /**
     * Get translated levels.
     *
     * @param  string|null  $locale
     *
     * @return array
     */
    public function names(?string $locale = null): array;

    /**
     * Get PSR log levels.
     *
     * @param  bool  $flip
     *
     * @return array
     */
    public static function all(bool $flip = false): array;

    /**
     * Get the translated level.
     *
     * @param  string       $key
     * @param  string|null  $locale
     *
     * @return string
     */
    public function get(string $key, ?string $locale = null): string;
}
