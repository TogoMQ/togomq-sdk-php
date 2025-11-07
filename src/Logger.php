<?php

declare(strict_types=1);

namespace TogoMQ\SDK;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Simple logger implementation for TogoMQ SDK
 */
class Logger implements LoggerInterface
{
    private const LEVEL_PRIORITY = [
        'none' => 0,
        LogLevel::DEBUG => 1,
        LogLevel::INFO => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
    ];

    private string $level;
    private int $levelPriority;

    /**
     * Create a new Logger instance
     *
     * @param string $level Log level (debug, info, warn, error, none)
     */
    public function __construct(string $level = 'info')
    {
        // Normalize 'warn' to 'warning' for PSR-3 compatibility
        $this->level = $level === 'warn' ? LogLevel::WARNING : $level;
        $this->levelPriority = self::LEVEL_PRIORITY[$this->level] ?? 2;
    }

    /**
     * Log a message if the level is enabled
     *
     * @param mixed $level Log level
     * @param string|\Stringable $message Log message
     * @param array<mixed> $context Additional context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if ($this->level === 'none') {
            return;
        }

        $messagePriority = self::LEVEL_PRIORITY[$level] ?? 0;
        if ($messagePriority < $this->levelPriority) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $levelStr = strtoupper((string) $level);
        $contextStr = ! empty($context) ? ' ' . json_encode($context) : '';

        echo sprintf("[%s] [%s] %s%s\n", $timestamp, $levelStr, $message, $contextStr);
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
