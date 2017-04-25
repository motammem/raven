<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Spider;

use Raven\Core\Util;
use Raven\Core\Spider;
use Raven\Content\Image;
use Raven\Core\DomCrawler;
use Raven\Core\Http\Request;
use GuzzleHttp\Psr7\Response;
use Raven\Content\Media\Media;
use Raven\Content\Article\Article;
use Raven\Content\Article\ArticlePipeline;
use League\Pipeline\PipelineBuilderInterface;
use Raven\Pipeline\TelegramPublisherPipeline;
use Raven\Pipeline\EloquentPersistencePipeline;
use Raven\Content\Media\Pipeline\MediaDownloaderPipeline;

class TestSpider extends Spider
{
    private $baseUrl = 'http://www.khabaronline.ir';

    public function getStartUrls()
    {
        return [$this->baseUrl.'/list/culture/music'];
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

        // return single requests
        foreach ($links as $link) {
            yield new Request(Util::urljoin($this->baseUrl, $link), [$this, 'parseSingle']);
        }

        // when done return next page
        $nextPageAnchor = $crawler->filter('ul.pagination li.active + li')->filter('a');
        if ($nextPageAnchor->count()) {
            $url = $nextPageAnchor->attr('href');
            yield new Request(Util::urljoin($this->baseUrl, $url), [$this, 'parse']);
        }
    }

    public function parseSingle(DomCrawler $crawler, Response $response, Request $request)
    {
        $matches = [];
        $identity = preg_match('/(?<=\/)\d{5,7}(?=\/)/', $request->getUri(), $matches) ? $matches[0] : null;
        $article = new Article($identity, [
            'title' => trim($crawler->filter('.newsTitle h2')->text()),
            'url' => $request->getUri(),
            'lead' => $crawler->filter('.leadCont')->count() ? $crawler->filter('.leadCont')->text() : null,
        ]);
        // main image
        if ($crawler->filter('.newsPhoto')->count()) {
            $mainMedia = new Media([
                'original_url' => $crawler->filter('.newsPhoto img')->attr('src'),
                'is_main' => 1,
            ]);
            $article->medias->add($mainMedia);
        }
        yield $article;
    }

    public function buildPipeline(PipelineBuilderInterface $builder)
    {
        $builder
            ->add(new ArticlePipeline())
//            ->add(new MediaDownloaderPipeline())
            ->add(new EloquentPersistencePipeline())
//            ->add(new TelegramPublisherPipeline())
;
    }
}
