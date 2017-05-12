<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core;

use Raven\Core\Http\Client;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Raven\Core\Schedule\CategorySequentialScheduler;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CrawlRunner
{
    public static function run()
    {
        $client = new Client();
        $dispatches = new EventDispatcher();
        $scheduler = new CategorySequentialScheduler();
        $crawler = new Crawler($client, $dispatches);
        _logger()->pushProcessor(new UidProcessor());
        _logger()->pushProcessor(new MemoryUsageProcessor());
        /** @var \Raven\Core\Spider\Spider $spider */
        foreach ($scheduler->getSpiders() as $spider) {
            _logger()->pushProcessor(
              function ($log) use ($spider) {
                  $log['extra'] = array_merge($spider->getContext(), $log['extra']);

                  return $log;
              }
            );
            $crawler->start($spider);
        }
        _logger()->popProcessor();
    }
}
