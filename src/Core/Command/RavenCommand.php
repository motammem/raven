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

use Raven\Core\Http\Client;
use Raven\Core\Strategy\Category\CategoryCrawler;
use Raven\Source\KhabarOnline\Category\Culture;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RavenCommand extends Command
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        global $logger;
        $catCrawler = new CategoryCrawler(new Client(), new EventDispatcher(), new Culture(), $logger);
        $catCrawler->start();
    }

    protected function configure()
    {
        $this->setName('run');
    }
}
