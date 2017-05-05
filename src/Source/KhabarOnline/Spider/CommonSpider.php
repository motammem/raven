<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Source\KhabarOnline\Spider;

use GuzzleHttp\Psr7\Response;
use League\Pipeline\PipelineBuilderInterface;
use Raven\Content\Article\Article;
use Raven\Content\Article\ArticlePipeline;
use Raven\Content\Media\Media;
use Raven\Core\DomCrawler;
use Raven\Core\Http\Request;
use Raven\Core\Spider;
use Raven\Pipeline\EloquentPersistencePipeline;
use Raven\Pipeline\TelegramPublisherPipeline;

class CommonSpider extends Spider\PaginatedSpider
{
    /**
     * @inheritDoc
     */
    protected function getSinglePageAnchor()
    {
        return '.row.media h4 a';
    }

    /**
     * @inheritDoc
     */
    protected function getNextPageAnchor()
    {
        return 'ul.pagination li.active + li a';
    }


    public function getStartUrls()
    {
        return ['http://www.khabaronline.ir/list/culture/music'];
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
            ->add(new TelegramPublisherPipeline())
            ->add(new EloquentPersistencePipeline())//            ->add(new TelegramPublisherPipeline())
        ;
    }
}
