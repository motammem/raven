<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Raven\Functional;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testLog()
    {
        $logger = new Logger('main');
        $logger->pushProcessor(function ($log) {
            var_dump($log['extra']);
            die();
        });

        $logger->info('SOMETHING HAPPENDED');
    }
}
