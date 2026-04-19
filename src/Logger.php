<?php

/*
 * Copyright (C) 2026 Katarzyna Krasińska
 * PHP.PSR-3.lab - https://github.com/katheroine/php.psr-3.lab
 * Licensed under GPL-3.0 - see LICENSE.md
 */

declare(strict_types=1);

namespace PHPLab\StandardPSR3;

class Logger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log(mixed $level, string|\Stringable $message, array $context = []): void
    {
        if (! in_array($level, array_values(new \ReflectionClass(\Psr\Log\LogLevel::class)->getConstants()))) {
            throw new \Psr\Log\InvalidArgumentException($level . ' signalisation level is improper.');
        }

        file_put_contents(
            __DIR__ . '/../tests/fixtures/var/log/psr3logger.log',
            '[' . date('Y-m-d H:i:s') . '] ' . strtoupper($level) . ': ' . $this->interpolateMessage($message, $context) . PHP_EOL,
            FILE_APPEND
        );
    }

    private function interpolateMessage(string|\Stringable $message, array $context): string
    {
        $replacements = [];
        foreach ($context as $placeholderLabel => $replacement) {
            $replacements['{' . $placeholderLabel . '}'] = $replacement;
        }

        return strtr((string) $message, $replacements);
    }
}
