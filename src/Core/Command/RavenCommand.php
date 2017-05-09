<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Command;

use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\UidProcessor;
use Raven\Core\Crawler;
use Raven\Core\Http\Client;
use Raven\Core\Schedule\CategorySequentialScheduler;
use Raven\Core\Spider\Spider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RavenCommand extends Command
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $dispatches = new EventDispatcher();
        $scheduler = new CategorySequentialScheduler();
        $crawler = new Crawler($client, $dispatches);
        _logger()->pushProcessor(new UidProcessor());
        _logger()->pushProcessor(new MemoryUsageProcessor());
        foreach ($scheduler->getSpiders() as $spider) {
            $this->setupLogging($spider);
            $crawler->start($spider);
        }
        _logger()->popProcessor();
    }


    protected function setupLogging(Spider $spider)
    {
        _logger()->pushProcessor(
          function ($log) use ($spider) {
              $log['extra'] = array_merge($spider->getContext(), $log['extra']);
              return $log;
          }
        );
    }

    protected function configure()
    {
        $this->setName('run');
    }
}
