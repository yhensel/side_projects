<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$environment = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'test';
$_SERVER['APP_ENV'] = $environment;
$_ENV['APP_ENV'] = $environment;

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if (($_SERVER['APP_DEBUG'] ?? '0') === '1' || ($_SERVER['APP_DEBUG'] ?? false) === true) {
    umask(0000);
}
