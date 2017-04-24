<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require_once __DIR__ . '/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
// setup environment variables
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// setup logger
$logger = new \Monolog\Logger('global', [
    new \Monolog\Handler\StreamHandler(__DIR__ . '/' . getenv('LOG_FILE')),
]);

// setup error and exception handlers
set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

function exception_handler(\Exception $exception)
{
    global $logger;
    $logger->addAlert($exception->getMessage(), [
        'exception' => $exception
    ]);
}

set_exception_handler('exception_handler');

// setup database
$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv("DATABASE_HOST"),
    'database'  => getenv("DATABASE_NAME"),
    'username'  => getenv("DATABASE_USERNAME"),
    'password'  => getenv("DATABASE_PASSWORD"),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();
