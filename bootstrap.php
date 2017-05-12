<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require_once __DIR__.'/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

/*
 * Helper functions
 */
function root_path($dir = null)
{
    return __DIR__.'/'.ltrim($dir, '/');
}


/*
 * Setup environment variables
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

/*
 * Setup system wide logger
 */
// stream handler
$streamHandler = new \Monolog\Handler\StreamHandler(
  root_path(getenv('LOG_FILE')), \Psr\Log\LogLevel::INFO
);
$formatter = new \Monolog\Formatter\LineFormatter();
$formatter->includeStacktraces();
$streamHandler->setFormatter($formatter);
ini_set('expect.timeout', 0);
// telegram handler
$telegramHandler = new \TelegramHandler\TelegramHandler(
  getenv("TELEGRAM_API_KEY"),
  '@kahbar_3anieh_dev',
  \Monolog\Logger::ERROR
);
$telegramHandler->setFormatter(new \TelegramHandler\TelegramFormatter());

// creating logger
$logger = new \Monolog\Logger(
  'raven', [
    $streamHandler,
    $telegramHandler,
  ]
);

// register logger to listen errors
\Monolog\ErrorHandler::register($logger);
function _log($level, $message, $context = [])
{
    global $logger;
    $logger->log($level, $message, $context);
}

function _logger()
{
    global $logger;

    return $logger;
}

/*
 * Setup database layer
 */
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

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();


