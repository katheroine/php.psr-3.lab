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
    public function log(mixed $level, string $message, array $context = []): void
    {
        file_put_contents(
            __DIR__ . '/../tests/fixtures/var/log/psr3logger.log',
            strtoupper($level) . ': Simple message.' . PHP_EOL
        );
    }
}
