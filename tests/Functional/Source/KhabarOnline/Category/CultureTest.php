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

class CultureTest extends TestCase
{
    public function testRun()
    {
        global $logger;
        $catCrawler = new CategoryCrawler(new Client(), new EventDispatcher(), $logger);
        $catCrawler->addCategory(new Culture());
        $catCrawler->start();
    }
}
