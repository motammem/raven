<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Spider;

use Raven\Core\Http\Request;
use Raven\Core\Http\Response;
use Raven\Core\Parse\DomCrawler;
use Raven\Core\Exception\SpiderCloseException;

abstract class PaginatedSpider extends Spider
{
    protected $startUrls = [];


    /**
     * @inheritDoc
     */
    public function getStartUrls()
    {
        return $this->startUrls;
    }
    public function addStartUrl($url)
    {
        $this->startUrls[] = $url;
    }

    /**
     * @var int
     */
    protected $currentPage = 1;

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
     * @param $link string Link of the resource
     *
     * @return string|void
     */
    protected function getIdentity($link)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(DomCrawler $crawler, Response $response, Request $request)
    {
        // if single page anchor exists
        $singlePages = $crawler->filter($this->getSinglePageAnchor());
        if ($singlePages->count()) {
            $singlePageLinks = [];
            // extract links
            $singlePages->each(function (DomCrawler $crawler) use (&$singlePageLinks) {
                $singlePageLinks[] = $crawler->attr('href');
            });

            // make request for each link
            foreach ($singlePageLinks as $link) {
                yield new Request($request->joinUrl($link), [$this, 'parseSingle'], $this->getIdentity($link));
            }
        }

        // if paginate strategy is enabled
        // and current page is equal to it's limit defined in strategy, then stop crawling!
        if (getenv('CRAWL_STRATEGY_PAGINATE') == 'enable' && $this->currentPage == getenv('CRAWL_STRATEGY_PAGINATE_LIMIT')) {
            throw new SpiderCloseException(sprintf('reached page limit %s', $this->currentPage));
        }

        // increase page counter
        ++$this->currentPage;

        // if next page anchor exist, request it's link
        $nextPage = $crawler->filter($this->getNextPageAnchor());
        if ($nextPage->count()) {
            yield new Request($request->joinUrl($nextPage->attr('href')), [$this, 'parse']);
        }
    }

    /**
     * @param \Raven\Core\Parse\DomCrawler $crawler
     * @param Response                     $response
     * @param Request                      $request
     *
     * @return mixed|Request[]
     */
    abstract public function parseSingle(DomCrawler $crawler, Response $response, Request $request);
}
