<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Raven\Functional\Source\KhabarOnline\Category;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Raven\Core\CategoryCrawler;
use Raven\Source\KhabarOnline\Category\Culture;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Raven\Core\Component\History\EventListener\HistoryEventSubscriber;

class CultureTest extends TestCase
{
    public function testRun()
    {
        global $logger;
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new HistoryEventSubscriber());
        $catCrawler = new CategoryCrawler(new Client(), $dispatcher, $logger);
        $catCrawler->addCategory(new Culture());
        $catCrawler->start();
    }
}
