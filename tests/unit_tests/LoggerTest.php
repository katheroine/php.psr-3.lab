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
use Psr\Log\LogLevel as Psr3LogLevel;

#[RunTestsInSeparateProcesses]
final class LoggerTest extends TestCase
{
    private const string LOG_LEVEL_EMERGENCY = 'emergency';

    private const string LOG_FILE_ABSOLUTE_PATH = __DIR__
        . DIRECTORY_SEPARATOR . '/../fixtures/var/log/psr3logger.log';

    /**
     * Instance of tested class.
     *
     * @var Logger
     */
    private Logger $logger;

    #[Test]
    #[DataProvider('properLogLevelsProvider')]
    public function logsMessageWithProperLevel(string $logLevel)
    {
        $message = 'Some message.';
        $date = date('Y-m-d H:i:s');

        $this->logger->log($logLevel, $message, []);

        $expectedLog = '[' . $date . '] ' . strtoupper($logLevel) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('improperLogLevelsProvider')]
    public function doesNotAllowForImproperLevel(string $logLevel)
    {
        $message = 'Some message.';

        $this->expectException(\Psr\Log\InvalidArgumentException::class);
        $this->logger->log($logLevel, $message, []);
    }

    #[Test]
    public function logsMessageWithProperDate()
    {
        $message = 'Some message.';

        $date1 = date('Y-m-d H:i:s');
        $this->logger->log(self::LOG_LEVEL_EMERGENCY, $message, []);

        $date2 = date('Y-m-d H:i:s');
        $this->logger->log(self::LOG_LEVEL_EMERGENCY, $message, []);

        $date3 = date('Y-m-d H:i:s');
        $this->logger->log(self::LOG_LEVEL_EMERGENCY, $message, []);

        $expectedLog =
            '[' . $date1 . '] ' . strtoupper(self::LOG_LEVEL_EMERGENCY) . ': ' . $message . PHP_EOL
            . '[' . $date2 . '] ' . strtoupper(self::LOG_LEVEL_EMERGENCY) . ': ' . $message . PHP_EOL
            . '[' . $date3 . '] ' . strtoupper(self::LOG_LEVEL_EMERGENCY) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    /**
     * Provide log levels defined by standard.
     *
     * @return array
     */
    public static function properLogLevelsProvider(): array
    {
        return [
            [Psr3LogLevel::EMERGENCY],
            [Psr3LogLevel::ALERT],
            [Psr3LogLevel::CRITICAL],
            [Psr3LogLevel::ERROR],
            [Psr3LogLevel::WARNING],
            [Psr3LogLevel::WARNING],
            [Psr3LogLevel::INFO],
            [Psr3LogLevel::DEBUG],
        ];
    }

    /**
     * Provide improper log levels.
     *
     * @return array
     */
    public static function improperLogLevelsProvider(): array
    {
        return [
            ['emergency room'],
            ['nerd alert'],
            ['hurricane'],
            ['narcissistic personality disorder'],
            ['Swedish deluge'],
            ['Microsoft Windows'],
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
