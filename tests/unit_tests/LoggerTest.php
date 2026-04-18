<?php

/*
 * Copyright (C) 2026 Katarzyna Krasińska
 * PHP.PSR-3.lab - https://github.com/katheroine/php.psr-3.lab
 * Licensed under GPL-3.0 - see LICENSE.md
 */

declare(strict_types=1);

namespace PHPLab\StandardPSR3;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class LoggerTest extends TestCase
{
    private const string LOG_FILE_ABSOLUTE_PATH = __DIR__
        . DIRECTORY_SEPARATOR . '/../fixtures/var/log/psr3logger.log';

    /**
     * Instance of tested class.
     *
     * @var Logger
     */
    private Logger $logger;

    #[Test]
    #[DataProvider('logLevelsProvider')]
    public function logsMessageWithProperLevel(string $logLevel)
    {
        $message = 'Simple message.';

        $this->logger->log($logLevel, $message, []);

        $expectedLog = strtoupper($logLevel) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    /**
     * Provide log levels defined by standard.
     *
     * @return array
     */
    public static function logLevelsProvider(): array
    {
        return [
            ['emergency'],
            ['alert'],
            ['critical'],
            ['error'],
            ['warning'],
            ['notice'],
            ['info'],
            ['debug'],
        ];
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->logger = new Logger();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        file_put_contents(self::LOG_FILE_ABSOLUTE_PATH, '');
    }

    private function getLoggedContent(): string
    {
        return file_get_contents(self::LOG_FILE_ABSOLUTE_PATH);
    }
}
