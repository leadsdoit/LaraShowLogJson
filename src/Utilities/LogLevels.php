<?php

declare(strict_types=1);

namespace Ldi\LogViewer\Utilities;

use Ldi\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Illuminate\Support\Arr;
use Illuminate\Translation\Translator;
use Psr\Log\LogLevel;
use ReflectionClass;

class LogLevels implements LogLevelsContract
{
    /**
     * The log levels.
     *
     * @var array
     */
    protected static array $levels = [];

    /**
     * The Translator instance.
     *
     * @var \Illuminate\Translation\Translator
     */
    private Translator $translator;

    /**
     * The selected locale.
     *
     * @var string
     */
    private string $locale;

    /**
     * LogLevels constructor.
     *
     * @param  \Illuminate\Translation\Translator  $translator
     * @param  string                              $locale
     */
    public function __construct(Translator $translator, string $locale)
    {
        $this->setTranslator($translator);
        $this->setLocale($locale);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the Translator instance.
     *
     * @param  \Illuminate\Translation\Translator  $translator
     *
     * @return $this
     */
    public function setTranslator(Translator $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Get the selected locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale === 'auto'
            ? $this->translator->getLocale()
            : $this->locale;
    }

    /**
     * Set the selected locale.
     *
     * @param  string  $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = is_null($locale) ? 'auto' : $locale;

        return $this;
    }

    /**
     * Get the log levels.
     *
     * @param  bool  $flip
     *
     * @return array
     */
    public function lists(bool $flip = false): array
    {
        return static::all($flip);
    }

    /**
     * Get translated levels.
     *
     * @param  string|null  $locale
     *
     * @return array
     */
    public function names(?string $locale = null): array
    {
        $levels = static::all(true);

        array_walk($levels, function (&$name, $level) use ($locale) {
            $name = $this->get($level, $locale);
        });

        return $levels;
    }

    /**
     * Get PSR log levels.
     *
     * @param  bool  $flip
     *
     * @return array
     */
    public static function all(bool $flip = false): array
    {
        if (empty(static::$levels)) {
            static::$levels = (new ReflectionClass(LogLevel::class))->getConstants();
        }

        return $flip ? array_flip(static::$levels) : static::$levels;
    }

    /**
     * Get the translated level.
     *
     * @param  string       $key
     * @param  string|null  $locale
     *
     * @return string
     */
    public function get(string $key, ?string $locale = null): string
    {
        $translations = [
            'all'               => 'All',
            LogLevel::EMERGENCY => 'Emergency',
            LogLevel::ALERT     => 'Alert',
            LogLevel::CRITICAL  => 'Critical',
            LogLevel::ERROR     => 'Error',
            LogLevel::WARNING   => 'Warning',
            LogLevel::NOTICE    => 'Notice',
            LogLevel::INFO      => 'Info',
            LogLevel::DEBUG     => 'Debug',
        ];

        return $this->translator->get(Arr::get($translations, $key, $key), [], $locale ?: $this->getLocale());
    }
}
