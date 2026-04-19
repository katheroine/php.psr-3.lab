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
        $this->logger->log(Psr3LogLevel::EMERGENCY, $message, []);

        $date2 = date('Y-m-d H:i:s');
        $this->logger->log(Psr3LogLevel::EMERGENCY, $message, []);

        $date3 = date('Y-m-d H:i:s');
        $this->logger->log(Psr3LogLevel::EMERGENCY, $message, []);

        $expectedLog =
            '[' . $date1 . '] ' . strtoupper(Psr3LogLevel::EMERGENCY) . ': ' . $message . PHP_EOL
            . '[' . $date2 . '] ' . strtoupper(Psr3LogLevel::EMERGENCY) . ': ' . $message . PHP_EOL
            . '[' . $date3 . '] ' . strtoupper(Psr3LogLevel::EMERGENCY) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('messagesProvider')]
    public function logsProperMessage(string $message)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, []);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('messagesAndContextsProvider')]
    public function logsMessageWithProperContext(string $message, array $context, string $expectedResult)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $expectedResult . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    /**
     * Provides allowed log levels defined by PSR-3 standard.
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
            [Psr3LogLevel::NOTICE],
            [Psr3LogLevel::INFO],
            [Psr3LogLevel::DEBUG],
        ];
    }

    /**
     * Provides random not allowed log levels.
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
     * Provides logged messages.
     *
     * @return array
     */
    public static function messagesProvider(): array
    {
        return [
            ['Hello, world!'],
            ['Greetings from the Moon.'],
            ['Penguins attack!'],
            ['Oh no, the technical debt is here'],
        ];
    }

    /**
     * Provides logged messages with placeholders
     * and sets of corresponding context values
     * with properly completed messages.
     *
     * @return array
     */
    public static function messagesAndContextsProvider(): array
    {
        return [
            [
                'message' => 'Videmus nunc {condition}!',
                'context' => [
                    'condition' => 'per speculum',
                ],
                'expectedResult' => 'Videmus nunc per speculum!'
            ],
            [
                'message' => 'Videmus nunc {condition}!',
                'context' => [
                    'condition' => 'in aenigmate',
                ],
                'expectedResult' => 'Videmus nunc in aenigmate!'
            ],
            [
                'message' => 'Omnis mundi {subiectum_1}, quasi {subiectum_2} et {subiectum_3}, nobis est in speculum.',
                'context' => [
                    'subiectum_1' => 'creatura',
                    'subiectum_2' => 'liber',
                    'unexistent_placeholder' => 'confitura',
                    'subiectum_3' => 'pictura',
                ],
                'expectedResult' => 'Omnis mundi creatura, quasi liber et pictura, nobis est in speculum.'
            ],
            [
                'message' => 'Omnis mundi {subiectum_1}, quasi {subiectum_2} et {subiectum_3}, nobis est in speculum.',
                'context' => [
                    'unexistent_placeholder' => 'agricultura',
                    'subiectum_1' => 'causa',
                    'subiectum_2' => 'arcanum',
                    'subiectum_3' => 'mysterium',
                ],
                'expectedResult' => 'Omnis mundi causa, quasi arcanum et mysterium, nobis est in speculum.'
            ],
            [
                'message' => 'Stat rosa pristina {1}, {2} nuda tenemus...',
                'context' => [
                    '2' => 'nomina',
                ],
                'expectedResult' => 'Stat rosa pristina {1}, nomina nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {1}, {2} nuda tenemus...',
                'context' => [
                    '3' => 'novina',
                ],
                'expectedResult' => 'Stat rosa pristina {1}, {2} nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {0}, {2} nuda tenemus...',
                'context' => [
                    'nomine',
                    2 => 'nomina'
                ],
                'expectedResult' => 'Stat rosa pristina nomine, nomina nuda tenemus...'
            ],
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
