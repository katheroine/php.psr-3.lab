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
docker exec --user root psr-3-example-app composer require --dev squizlabs/php_codesniffer --dev phpunit/phpunit \
&& docker exec --user root psr-3-example-app composer install
```

Tom make *PHP Code Sniffer commands* easily accessible run:

```console
docker exec --user root psr-3-example-app bash -c "
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
docker exec --user root psr-3-example-app composer update
```

To update *Composer autoloader* cache use use command:

```console
docker exec --user root psr-3-example-app composer dump-autoload
```

To login into the *Docker container* as default user use command:

```console
docker exec -it psr-3-example-app /bin/bash
```

To login into the *Docker container* as root user use command:

```console
docker exec --user root -it psr-3-example-app /bin/bash
```

**License**

This project is licensed under the GPL-3.0 - see [LICENSE](LICENSE).
