<?php

declare(strict_types=1);

namespace Ldi\LogSpaViewer\Entities;

use Ldi\LogSpaViewer\Helpers\LogParser;
use Carbon\Carbon;
use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use JsonSerializable;

class LogEntry implements Arrayable, Jsonable, JsonSerializable
{
    public string $env;

    public string $level;

    public Carbon $datetime;

    public string $header;

    public string $stack;

    /** @var array */
    public array $context = [];

    public function __construct(string $level, string $header, ?string $stack = null)
    {
        $this->setLevel($level);
        $this->setHeader($header);
        $this->setStack($stack);
    }


    private function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    private function setHeader(string $header): self
    {
        $this->setDatetime($this->extractDatetime($header));

        $header = $this->cleanHeader($header);

        $this->header = trim($header);

        return $this;
    }

    private function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    private function setEnv(string $env): self
    {
        $this->env = head(explode('.', $env));

        return $this;
    }

    private function setDatetime(string $datetime): self
    {
        $this->datetime = Carbon::createFromFormat('Y-m-d H:i:s', $datetime);

        return $this;
    }

    private function setStack(string $stack): self
    {
        $this->stack = $stack;

        return $this;
    }

    public function name(): string
    {
        return log_levels()->get($this->level);
    }

    public function context(int $options = JSON_PRETTY_PRINT): string
    {
        return json_encode($this->context, $options);
    }

    public function isSameLevel(string $level): bool
    {
        return $this->level === $level;
    }

    public function toArray(): array
    {
        return [
            'env' => $this->env,
            'level'    => $this->level,
            'datetime' => $this->datetime->format('Y-m-d H:i:s'),
            'header'   => $this->header,
            'stack'    => $this->stack
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function hasStack(): bool
    {
        return $this->stack !== "\n";
    }

    public function hasContext(): bool
    {
        return ! empty($this->context);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    private function cleanHeader(string $header): string
    {
        // REMOVE THE DATE
        $header = preg_replace('/\['.LogParser::REGEX_DATETIME_PATTERN.'\][ ]/', '', $header);

        // EXTRACT ENV
        if (preg_match('/^[a-z]+.[A-Z]+:/', $header, $out)) {
            $this->setEnv($out[0]);
            $header = trim(str_replace($out[0], '', $header));
        }

        // EXTRACT CONTEXT (Regex from https://stackoverflow.com/a/21995025)
        preg_match_all('/{(?:[^{}]|(?R))*}/x', $header, $out);
        if (isset($out[0][0]) && ! is_null($context = json_decode($out[0][0], true))) {
            $header = str_replace($out[0][0], '', $header);
            $this->setContext($context);
        }

        return $header;
    }

    private function extractDatetime(string $header): string
    {
        return preg_replace('/^\[('.LogParser::REGEX_DATETIME_PATTERN.')\].*/', '$1', $header);
    }
}
