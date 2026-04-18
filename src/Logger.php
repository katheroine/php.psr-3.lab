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
    }
}
