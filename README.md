# PHP.PSR-3.lab

Laboratory of PSR-3: Logger Interface.

> This repository is a standalone part of a larger project: **[PHP.lab](https://github.com/katheroine/php.lab)** — a curated knowledge base and laboratory for PHP engineering.

**Usage**

To run the example application with *Docker* use command:

```console
docker compose up -d
```

After creating the *Docker container* the *Composer dependencies* have to be defined and installed:

```console
docker compose exec application composer require --dev squizlabs/php_codesniffer --dev phpunit/phpunit \
&& docker compose exec application composer install
```

Tom make *PHP Code Sniffer commands* easily accessible run:

```console
docker compose exec application bash -c "
    ln -s /code/vendor/bin/phpcs /usr/local/bin/phpcs;
    ln -s /code/vendor/bin/phpcbf /usr/local/bin/phpcbf;
    ln -s /code/vendor/bin/phpunit /usr/local/bin/phpunit;
"
```

To run *PHP Code Sniffer* use command:

```console
docker exec psr-3-example-app /code/vendor/bin/phpcs
```

or, if the shortcut has been created:

```console
docker exec psr-3-example-app phpcs
```

To run *PHP Unit* use command:

```console
docker exec psr-3-example-app /code/vendor/bin/phpunit
```

or, if the shortcut has been created:

```console
docker exec psr-3-example-app phpunit
```

To update *Composer dependencies* use command:

```console
docker exec psr-3-example-app composer update
```

To update *Composer autoloader* cache use use command:

```console
docker exec psr-3-example-app composer dump-autoload
```

To login into the *Docker container* use command:

```console
docker exec -it psr-3-example-app /bin/bash
```

**License**

This project is licensed under the GPL-3.0 - see [LICENSE](LICENSE).

**Official documentation**

[PHP-FIG PSR-3 Official documentation](https://www.php-fig.org/psr/psr-3/)

**What are PSRs**

[**PSR**](https://www.php-fig.org/psr/) stands for *PHP Standard Recommendation*.

# PHP.PSR-3.lab

Laboratory of PSR-3: Logger Interface.

> This repository is a standalone part of a larger project: **[PHP.lab](https://github.com/katheroine/php.lab)** — a curated knowledge base and laboratory for PHP engineering.

**Official documentation**

[PHP-FIG PSR-3 Official documentation](https://www.php-fig.org/psr/psr-3/)

**What are PSRs**

[**PSR**](https://www.php-fig.org/psr/) stands for *PHP Standard Recommendation*.

## Overview

This PSR describes a common interface for logging libraries. The main goal is to allow libraries to receive a `Psr\Log\LoggerInterface` object and write logs to it in a simple and universal way. Frameworks and CMSs that have custom needs MAY extend the interface for their own purpose, but SHOULD remain compatible with this document. This ensures that the third-party libraries an application uses can write to the centralized application logs.

The word `implementor` in this document is to be interpreted as someone implementing the `LoggerInterface` in a log-related library or framework. Users of loggers are referred to as `user`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#logger-interface)

## Methods

The `LoggerInterface` exposes **eight methods** to write logs, corresponding to the eight [RFC 5424](http://tools.ietf.org/html/rfc5424) severity levels:

| Method | Severity | Description |
|---|---|---|
| `emergency($message, $context)` | Emergency | System is unusable. |
| `alert($message, $context)` | Alert | Action must be taken immediately. Example: entire website down, database unavailable. |
| `critical($message, $context)` | Critical | Critical conditions. Example: application component unavailable, unexpected exception. |
| `error($message, $context)` | Error | Runtime errors that do not require immediate action but should typically be logged and monitored. |
| `warning($message, $context)` | Warning | Exceptional occurrences that are not errors. Example: use of deprecated APIs, poor use of an API. |
| `notice($message, $context)` | Notice | Normal but significant events. |
| `info($message, $context)` | Info | Interesting events. Example: user logs in, SQL logs. |
| `debug($message, $context)` | Debug | Detailed debug information. |

A **ninth method**, `log`, accepts a log level as its first argument:

```php
public function log($level, $message, array $context = array());
```

* Calling `log` with one of the log level constants **MUST** produce the same result as calling the corresponding level-specific method.
* Calling `log` with an unrecognized level **MUST** throw a `Psr\Log\InvalidArgumentException`.
* Users **SHOULD NOT** use a custom level without knowing for sure that the current implementation supports it.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#1-specification)

**`LoggerInterface`**

```php
<?php

namespace Psr\Log;

interface LoggerInterface
{
    public function emergency($message, array $context = array());
    public function alert($message, array $context = array());
    public function critical($message, array $context = array());
    public function error($message, array $context = array());
    public function warning($message, array $context = array());
    public function notice($message, array $context = array());
    public function info($message, array $context = array());
    public function debug($message, array $context = array());
    public function log($level, $message, array $context = array());
}
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#3-psrlogloggerinterface)

## Message

Every method accepts a string as the message, or an object with a `__toString()` method.

* Implementors **MAY** have special handling for the passed objects. If that is not the case, implementors **MUST** cast it to a string.
* The message **MAY** contain placeholders which implementors **MAY** replace with values from the context array.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#12-message)

**Design intent**: The message passed to a logging method should always be a *static value*. Any context-specific variability (such as a username, timestamp, or other information) should be provided via the `$context` array only, and the string should use a placeholder to reference it.

This design is intentional for two reasons:

1. The message is readily available to translation systems to create localized versions of log messages.
2. Context-specific data may contain user input, and thus requires escaping — which will differ depending on whether the log is stored in a database, serialized to JSON, written to syslog, etc. It is the responsibility of the logging implementation to ensure that `$context` data shown to the user is appropriately escaped.

-- [PSR Meta Documentation](https://www.php-fig.org/psr/psr-3/meta/#static-log-messages)

## Log level

The eight log levels defined by [RFC 5424](http://tools.ietf.org/html/rfc5424) are available as constants in the `Psr\Log\LogLevel` class:

```php
<?php

namespace Psr\Log;

class LogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
}
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#5-psrlogloglevel)

## Context

Every method accepts an array as context data. This is meant to hold any extraneous information that does not fit well in a string. The array can contain anything.

* Implementors **MUST** ensure they treat context data with as much lenience as possible.
* A given value in the context **MUST NOT** throw an exception nor raise any PHP error, warning, or notice.
* If an `Exception` object is passed in the context data, it **MUST** be in the `'exception'` key. Logging exceptions is a common pattern and this allows implementors to extract a stack trace from the exception when the log backend supports it.
* Implementors **MUST** still verify that the `'exception'` key is actually an `Exception` before using it as such, as it **MAY** contain anything.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#13-context)

### Placeholder label

Placeholder names **MUST** correspond to keys in the context array.

Placeholder names **MUST** be delimited with a single opening brace `{` and a single closing brace `}`. There **MUST NOT** be any whitespace between the delimiters and the placeholder name.

Placeholder names **SHOULD** be composed only of the characters `A-Z`, `a-z`, `0-9`, underscore `_`, and period `.`. The use of other characters is reserved for future modifications of the placeholders specification.

```
{placeholder}     ✔  valid
{ placeholder }   ✖  invalid — whitespace inside braces
{place.holder_1}  ✔  valid — period and underscore are allowed
{place holder}    ✖  invalid — space is not an allowed character
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#12-message)

### Replacements

Implementors **MAY** use placeholders to implement various escaping strategies and translate logs for display.

Users **SHOULD NOT** pre-escape placeholder values since they cannot know in which context the data will be displayed.

The following is a reference implementation of placeholder interpolation:

```php
<?php

/**
 * Interpolates context values into the message placeholders.
 */
function interpolate($message, array $context = array())
{
    // build a replacement array with braces around the context keys
    $replace = array();
    foreach ($context as $key => $val) {
        // check that the value can be cast to string
        if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
            $replace['{' . $key . '}'] = $val;
        }
    }

    // interpolate replacement values into the message and return
    return strtr($message, $replace);
}

// a message with brace-delimited placeholder names
$message = "User {username} created";

// a context array of placeholder names => replacement values
$context = array('username' => 'bolivar');

// echoes "User bolivar created"
echo interpolate($message, $context);
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#12-message)

#### Throwable vs. Exception

At the time PSR-3 was written, PHP only had the `Exception` type. In modern PHP, `Throwable` is the common base interface for both `Exception` and `Error`.

Wherever this specification refers to an `Exception` being passed in the `exception` context key, it **SHOULD** be interpreted as allowing any `Throwable` instance. Implementations **MUST** still verify that the value in the `exception` context key is actually a `Throwable` before using it as such.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/meta/#52-throwable-vs-exception)

## Helper classes and interfaces

PSR-3 ships with a set of helper classes and interfaces to simplify implementation:

| Class / Interface | Purpose |
|---|---|
| `Psr\Log\AbstractLogger` | Extend it and implement only the generic `log` method — the other eight methods delegate to it automatically. |
| `Psr\Log\LoggerTrait` | Requires only the generic `log` method to be implemented. Note: traits cannot implement interfaces, so `LoggerInterface` must still be declared explicitly. |
| `Psr\Log\NullLogger` | A "black hole" fall-back implementation that discards all log messages. Useful when no logger is provided, though conditional logging may be preferable when context data creation is expensive. |
| `Psr\Log\LoggerAwareInterface` | Contains a single `setLogger(LoggerInterface $logger)` method; allows frameworks to auto-wire instances with a logger. |
| `Psr\Log\LoggerAwareTrait` | Implements `LoggerAwareInterface` easily in any class and exposes `$this->logger`. |
| `Psr\Log\LogLevel` | Holds constants for the eight log levels. |

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#14-helper-classes-and-interfaces)

## Using in the code

**`LoggerAwareInterface`**

```php
<?php

namespace Psr\Log;

interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger);
}
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#4-psrlogloggerawareinterface)

## Package

The interfaces, classes, exception classes, and a test suite to verify your implementation are provided as part of the [`psr/log`](https://packagist.org/packages/psr/log) Composer package.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/#2-package)

## Type additions

The `psr/log` package has evolved across major versions with regard to type declarations:

* **v2.0** — scalar parameter types were added.
* **v3.0** — return types were added. Requires PHP 8.0 for full type compatibility (leveraging PHP 7.2 covariance for a gradual upgrade path).

Implementors **MAY** add return types or parameter types to their own packages provided that:

* the types match those in the 3.0 / 2.0 package respectively.
* the implementation specifies a minimum PHP version of 8.0.0 or later.
* when adding parameter types, the implementation depends on `"psr/log": "^2.0 || ^3.0"` to exclude the untyped 1.0 version.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-3/meta/#51-type-additions)
