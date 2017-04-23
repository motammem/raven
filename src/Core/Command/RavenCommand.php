<?php

/*
 *
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Raven\Core\Command;

use Monolog\Logger;
use GuzzleHttp\Client;
use Raven\Core\Crawler;
use Raven\Spider\TestSpider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RavenCommand extends Command
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $crawler = new Crawler(new Client());
        $testSpider = new TestSpider();
        $testSpider->setLogger(new Logger('test-spider'));
        $crawler->setSpiders([
            $testSpider,
        ]);
        $crawler->start();
    }

    protected function configure()
    {
        $this->setName('run');
    }
}
