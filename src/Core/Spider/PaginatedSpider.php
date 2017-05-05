<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Core\Spider;

use GuzzleHttp\Psr7\Response;
use Raven\Core\DomCrawler;
use Raven\Core\Http\Request;
use Raven\Core\Spider;

abstract class PaginatedSpider extends Spider
{
    /**
     * @return string CSS selector of single page anchor last element of selector should end with `a` tag
     */
    abstract protected function getSinglePageAnchor();

    /**
     * @return string CSS selector of next page anchor last element of selector should end with `a` tag
     */
    abstract protected function getNextPageAnchor();

    /**
     * Identity of the resource, if it's same as url leave it blank
     * This option is used when a resource have more than one uri pointed to it.
     *
     * @param $link string Link of the resource.
     * @return string|void
     */
    protected function getIdentity($link)
    {
    }

    /**
     * @inheritDoc
     */
    public function parse(DomCrawler $crawler, Response $response, Request $request)
    {
        $singlePages = $crawler->filter($this->getSinglePageAnchor());
        if ($singlePages->count()) {
            $singlePageLinks = [];
            $singlePages->each(function (DomCrawler $crawler) use (&$singlePageLinks) {
                $singlePageLinks[] = $crawler->attr('href');
            });
            foreach ($singlePageLinks as $link) {
                yield new Request($request->joinUrl($link), [$this, 'parseSingle'], $this->getIdentity($link));
            }
        }

        $nextPage = $crawler->filter($this->getNextPageAnchor());
        if ($nextPage->count()) {
            yield new Request($request->joinUrl($nextPage->attr('href')), [$this, 'parse']);
        }
    }

    /**
     * @param DomCrawler $crawler
     * @param Response $response
     * @param Request $request
     *
     * @return mixed|Request[]
     */
    abstract protected function parseSingle(DomCrawler $crawler, Response $response, Request $request);
}