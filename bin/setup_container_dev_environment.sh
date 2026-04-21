# After creating the *Docker container* the *Composer dependencies* have to be defined and installed:

docker exec --user root psr-3-example-app composer require --dev squizlabs/php_codesniffer --dev phpunit/phpunit \
&& docker exec --user root psr-3-example-app composer install

# To make *PHP Code Sniffer commands* easily accessible run:

docker exec --user root psr-3-example-app bash -c "
    ln -s /code/vendor/bin/phpcs /usr/local/bin/phpcs;
    ln -s /code/vendor/bin/phpcbf /usr/local/bin/phpcbf;
    ln -s /code/vendor/bin/phpunit /usr/local/bin/phpunit;
"
