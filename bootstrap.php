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
$streamHandler = new \Monolog\Handler\StreamHandler(__DIR__ . '/' . getenv('LOG_FILE'));
$formatter = new \Monolog\Formatter\LineFormatter();
$formatter->includeStacktraces();
$streamHandler->setFormatter($formatter);
$telegramHandler = new \TelegramHandler\TelegramHandler(getenv("TELEGRAM_API_KEY"), '@khabar_3anieh_dev',
    \Monolog\Logger::ALERT);
$logger = new \Monolog\Logger('global', [
    $streamHandler,
    $telegramHandler,
]);

\Monolog\ErrorHandler::register($logger, [], \Psr\Log\LogLevel::ALERT);
\Symfony\Component\Debug\ExceptionHandler::register();

// setup database
$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => getenv("DATABASE_HOST"),
    'database' => getenv("DATABASE_NAME"),
    'username' => getenv("DATABASE_USERNAME"),
    'password' => getenv("DATABASE_PASSWORD"),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

function root_path($dir = null)
{
    return __DIR__ . '/' . ltrim($dir, '/');
}