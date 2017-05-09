<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Raven\Functional\Core\Spider;

use Raven\Core\Http\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Raven\Core\Parse\DomCrawler;
use Raven\Core\Spider\PaginatedSpider;

class PaginatedSpiderTest extends TestCase
{
    /**
     * @var PaginatedSpider
     */
    private $paginatedSpider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->paginatedSpider = \Mockery::mock(PaginatedSpider::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSinglePageAnchor')->andReturn('ul.single li a')->getMock()
            ->shouldReceive('getNextPageAnchor')->andReturn('a.next-page')->getMock();
    }

    public function testParse()
    {
        $html = file_get_contents(__DIR__.'/_data/paginated-spider.html');
        $links = [
            '/page_1.html',
            '/page_2.html',
            '/page_3.html',
            '/next_page.html',
        ];
        /** @var Request[] $generator */
        $generator = $this->paginatedSpider->parse(new DomCrawler($html), new Response(), new Request('', ''));
        $parsedLinks = [];
        foreach ($generator as $request) {
            $parsedLinks[] = (string) $request->getUri();
        }

        $this->assertSame($links, $parsedLinks);
    }
}
