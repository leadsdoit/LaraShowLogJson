<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Utilities;

use Ldi\LogSpaViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Illuminate\Support\Arr;
use Illuminate\Translation\Translator;
use Psr\Log\LogLevel;
use ReflectionClass;

class LogLevels implements LogLevelsContract
{
    protected static array $levels = [];
    private Translator $translator;

    private string $locale;

    public function __construct(Translator $translator, string $locale)
    {
        $this->setTranslator($translator);
        $this->setLocale($locale);
    }

    public function setTranslator(Translator $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale === 'auto'
            ? $this->translator->getLocale()
            : $this->locale;
    }
    public function setLocale(string $locale): self
    {
        $this->locale = is_null($locale) ? 'auto' : $locale;

        return $this;
    }

    public function lists(bool $flip = false): array
    {
        return static::all($flip);
    }

    public function names(?string $locale = null): array
    {
        $levels = static::all(true);

        array_walk($levels, function (&$name, $level) use ($locale) {
            $name = $this->get($level, $locale);
        });

        return $levels;
    }

    public static function all(bool $flip = false): array
    {
        if (empty(static::$levels)) {
            static::$levels = (new ReflectionClass(LogLevel::class))->getConstants();
        }

        return $flip ? array_flip(static::$levels) : static::$levels;
    }

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
