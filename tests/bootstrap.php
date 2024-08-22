<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// TODO: Should be redone to use only in functional and application tests.
//       It must not be loaded for unit tests !
passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" doctrine:database:create --if-not-exists',
    $_ENV['APP_ENV'],
    __DIR__
));

passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" doctrine:schema:drop --force --quiet',
    $_ENV['APP_ENV'],
    __DIR__
));

passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" doctrine:schema:create --quiet',
    $_ENV['APP_ENV'],
    __DIR__
));

passthru(sprintf(
    'APP_ENV=%s php "%s/../bin/console" doctrine:fixtures:load --quiet',
    $_ENV['APP_ENV'],
    __DIR__
));
