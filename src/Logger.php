<?php

/*
 * Copyright (C) 2026 Katarzyna Krasińska
 * PHP.PSR-3.lab - https://github.com/katheroine/php.psr-3.lab
 * Licensed under GPL-3.0 - see LICENSE.md
 */

declare(strict_types=1);

namespace PHPLab\StandardPSR3;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use Stringable;
use Exception;
use ReflectionClass;

class Logger implements LoggerInterface
{
    /**
     * Logs file absolute path.
     *
     * @var string
     */
    private string $logsFilePath;

    public function __construct(string $logsFilePath)
    {
        $this->logsFilePath = $logsFilePath;
    }

    /**
     * System is unusable.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->validateLevel($level);

        $log = $this->formatLog($level, $message, $context);
        $this->writeLog($log);
    }

    /**
     * Validate if level is compliant with the PSR-3 specification.
     */
    private static function validateLevel(mixed $level): void
    {
        if (! in_array($level, array_values(new ReflectionClass(LogLevel::class)->getConstants()))) {
            throw new InvalidArgumentException($level . ' signalisation level is improper.');
        }
    }

    /**
     * Format log content and prepare for being written down.
     */
    private function formatLog(mixed $level, string|Stringable $message, array $context): string
    {
        return '[' . date('Y-m-d H:i:s') . '] '
            . strtoupper($level)
            . ': ' . $this->interpolateMessage($message, $context) . PHP_EOL;
    }

    /**
     * Replace placeholders with context replacements.
     */
    private function interpolateMessage(string|Stringable $message, array $context): string
    {
        $replacements = [];
        foreach ($context as $placeholderLabel => $replacement) {
            if (
                ! $this->isPlaceholderLabelValid($placeholderLabel)
                || ! $this->isReplacementValid($replacement)
            ) {
                continue;
            }

            if ($replacement instanceof Exception) {
                $replacement = $replacement->getMessage();
            }

            $replacements['{' . $placeholderLabel . '}'] = $replacement;
        }

        return strtr((string) $message, $replacements);
    }

    /**
     * Checks if placeholder label it compliant with the PSR-3 specification rule:
     *
     * Placeholder names SHOULD be composed only of the characters A-Z, a-z, 0-9,
     * underscore _, and period ..
     * The use of other characters is reserved for future modifications of the placeholders specification.
     */
    private function isPlaceholderLabelValid(mixed $placefolderLabel): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_.]+$/', (string) $placefolderLabel);
    }

    /**
     * Checks if the replacement can be used as a string.
     */
    private function isReplacementValid(mixed $replacement): bool
    {
        return (is_null($replacement) || is_scalar($replacement) || ($replacement instanceof Stringable));
    }

    /**
     * Writes down the log content to the log file.
     */
    private function writeLog(string $logContent): void
    {
        file_put_contents($this->logsFilePath, $logContent, FILE_APPEND);
    }
}
