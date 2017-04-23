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

namespace Raven\Spider;

use Raven\Core\Util;
use Raven\Core\Spider;
use Raven\Content\Image;
use Raven\Core\DomCrawler;
use Raven\Core\Http\Request;
use GuzzleHttp\Psr7\Response;
use Raven\Content\Article\Article;
use League\Pipeline\PipelineBuilderInterface;
use Raven\Pipeline\TelegramPublisherPipeline;

class TestSpider extends Spider
{
    private $baseUrl = 'http://www.khabaronline.ir';

    public function getStartUrls()
    {
        return [$this->baseUrl.'/list/Economy/political-economy'];
    }

    public function parse(DomCrawler $crawler, Response $response, Request $request)
    {
        //        throw new SpiderCloseException('reached duplicated content');
        $links = [];
        $crawler->filter('.row.media h4 a')->each(function (DomCrawler $crawler) use (&$links) {
            $rel = $crawler->attr('href');
            if ($rel) {
                $links[] = $rel;
            }
        });
        foreach ($links as $link) {
            yield new Request(Util::urljoin($this->baseUrl, $link), [$this, 'parseSingle']);
        }
    }

    public function parseSingle(DomCrawler $crawler, Response $response, Request $request)
    {
        yield new Article(
            $crawler->filter('.newsTitle h2')->text(),
            $crawler->filter('.leadCont')->count() ? $crawler->filter('.leadCont')->text() : null,
            $crawler->filter('.newsPhoto')->count() ? new Image($crawler->filter('.newsPhoto img')->attr('src')) : null
        );
    }

    public function buildPipeline(PipelineBuilderInterface $builder)
    {
        $builder->add(new TelegramPublisherPipeline());
    }
}
