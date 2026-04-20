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
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel as Psr3LogLevel;

final class LoggerTest extends TestCase
{
    private const string LOGS_DIRECTORY_ABSOLUTE_PATH = __DIR__
        . DIRECTORY_SEPARATOR . '../fixtures/var/log/';
    private const string LOG_FILE_ABSOLUTE_PATH = self::LOGS_DIRECTORY_ABSOLUTE_PATH
        . 'psr3logger.log';

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
    public function logsStringableMessageProperly()
    {
        $date = date('Y-m-d H:i:s');

        $message = new class { public function __toString() { return 'Hi, there!'; } };
        $this->logger->log(Psr3LogLevel::INFO, $message, []);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . (string) $message . PHP_EOL;
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

    #[Test]
    public function improperlyDelimitedPlaceholderIsSkippedInInterpolation()
    {
        $date = date('Y-m-d H:i:s');

        $message = "A { skipped } thing.";
        $context = [
            'skipped' => 'replaced',
        ];
        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('properPlaceholderLabelsAndMessagesProvider')]
    public function properPlaceholderLabelIsUsedInInterpolation(string $message, array $context, string $expectedResult)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $expectedResult . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('improperPlaceholderLabelsAndMessagesProvider')]
    public function improperPlaceholderLabelIsSkippedInInterpolation(string $message, array $context)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('stringablePlaceholderLabelsAndMessagesProvider')]
    public function stringablePlaceholderLabelIsUsedInInterpolation(string $message, array $context, string $expectedResult)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $expectedResult . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('stringableReplacementsAndMessagesProvider')]
    public function stringableReplacementIsUsedInInterpolation(string $message, array $context, string $expectedResult)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $expectedResult . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('nonstringableReplacementsAndMessagesProvider')]
    public function nonstringableReplacementIsSkippedInInterpolation(string $message, array $context)
    {
        $date = date('Y-m-d H:i:s');

        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': ' . $message . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    public function exceptionPlaceholderLabelIsUsedInInterpolation()
    {
        $date = date('Y-m-d H:i:s');

        $message = "A {exception} thing.";
        $context = [
            'exception' => 'replaced',
        ];
        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': A replaced thing.' . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    public function exceptionReplacementIsUsedInInterpolation()
    {
        $date = date('Y-m-d H:i:s');

        $message = "A {placeholder} thing.";
        $context = [
            'placeholder' => new \Exception('replaced'),
        ];
        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': A replaced thing.' . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    public function exceptionPlaceholderLabelAndReplacementIsUsedInInterpolation()
    {
        $date = date('Y-m-d H:i:s');

        $message = "A {exception} thing.";
        $context = [
            'exception' => new \Exception('replaced'),
        ];
        $this->logger->log(Psr3LogLevel::INFO, $message, $context);

        $expectedLog = '[' . $date . '] ' . strtoupper(Psr3LogLevel::INFO) . ': A replaced thing.' . PHP_EOL;
        $actualLog = $this->getLoggedContent();

        $this->assertEquals($expectedLog, $actualLog);
    }

    #[Test]
    #[DataProvider('logFileNamesProvider')]
    public function usesProvidedLogFilePath(string $logFileName)
    {
        $logFilePath = self::LOGS_DIRECTORY_ABSOLUTE_PATH . $logFileName;
        $logger = new Logger($logFilePath);

        $logger->log(Psr3LogLevel::INFO, 'Some message.', []);

        $this->assertFileExists($logFilePath);
        $this->assertStringContainsString('Some message.', file_get_contents($logFilePath));

        file_put_contents($logFilePath, '');
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
     * with sets of corresponding context values
     * and expected interpolation result.
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
     * Provides placeholders compliant with the PSR-3 specification rule:
     *
     * Placeholder names SHOULD be composed only of the characters A-Z, a-z, 0-9,
     * underscore _, and period ..
     * The use of other characters is reserved for future modifications of the placeholders specification.
     *
     * with using them messages
     * and expected interpolation result.
     *
     * @return array
     */
    public static function properPlaceholderLabelsAndMessagesProvider(): array
    {
        return [
            [
                'message' => 'Stat rosa pristina {name}, nuda tenemus...',
                'context' => ['name' => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {NAME}, nuda tenemus...',
                'context' => ['NAME' => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {name_1}, nuda tenemus...',
                'context' => ['name_1' => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {name.version}, nuda tenemus...',
                'context' => ['name.version' => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {Name_Version.1}, nuda tenemus...',
                'context' => ['Name_Version.1' => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nuda tenemus...'
            ],
        ];
    }

    /**
     * Provides placeholders not compliant with the PSR-3 specification rule:
     *
     * Placeholder names SHOULD be composed only of the characters A-Z, a-z, 0-9,
     * underscore _, and period ..
     * The use of other characters is reserved for future modifications of the placeholders specification.
     *
     * with using them messages.
     *
     * @return array
     */
    public static function improperPlaceholderLabelsAndMessagesProvider(): array
    {
        return [
            [
                'message' => 'Stat rosa pristina {invalid-key}, nuda tenemus...',
                'context' => ['invalid-key' => 'nomine']
            ],
            [
                'message' => 'Stat rosa pristina {invalid key}, nuda tenemus...',
                'context' => ['invalid key' => 'nomine']
            ],
            [
                'message' => 'Stat rosa pristina {invalid@key}, nuda tenemus...',
                'context' => ['invalid@key' => 'nomine']
            ],
        ];
    }

    /**
     * Provides stringable placeholders
     * with using them messages.
     *
     * @return array
     */
    public static function stringablePlaceholderLabelsAndMessagesProvider(): array
    {
        return [
            [
                'message' => 'Stat rosa pristina {1}, nomina nuda tenemus...',
                'context' => [1 => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nomina nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {0}, nomina nuda tenemus...',
                'context' => [0 => 'nomine'],
                'expectedResult' => 'Stat rosa pristina nomine, nomina nuda tenemus...'
            ],
        ];
    }

    /**
     * Provides stringable replacements
     * with using them messages.
     *
     * @return array
     */
    public static function stringableReplacementsAndMessagesProvider(): array
    {
        return [
            [
                'message' => 'Stat rosa pristina {name}, nomina nuda tenemus...',
                'context' => ['name' => 42],
                'expectedResult' => 'Stat rosa pristina 42, nomina nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {name}, nomina nuda tenemus...',
                'context' => ['name' => 3.14],
                'expectedResult' => 'Stat rosa pristina 3.14, nomina nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {name}, nomina nuda tenemus...',
                'context' => ['name' => true],
                'expectedResult' => 'Stat rosa pristina 1, nomina nuda tenemus...'
            ],
            [
                'message' => 'Stat rosa pristina {name}, nomina nuda tenemus...',
                'context' => ['name' => new class { public function __toString(): string { return 'nomine'; } }],
                'expectedResult' => 'Stat rosa pristina nomine, nomina nuda tenemus...'
            ],
        ];
    }

    /**
     * Provides non-stringable replacements.
     *
     * @return array
     */
    public static function nonstringableReplacementsAndMessagesProvider(): array
    {
        return [
            [
                'message' => 'Stat rosa pristina {name}, nuda tenemus...',
                'context' => ['name' => ['nomine']]
            ],
            [
                'message' => 'Stat rosa pristina {name}, nuda tenemus...',
                'context' => ['name' => new \stdClass()]
            ],
        ];
    }

    /**
     * Provides non-stringable replacements.
     *
     * @return array
     */
    public static function logFileNamesProvider(): array
    {
        return [
            ['destiny_1.log'],
            ['destiny_2.log'],
            ['destiny_3.log'],
        ];
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->logger = new Logger(self::LOG_FILE_ABSOLUTE_PATH);
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
