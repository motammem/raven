<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Source\Zoomit\Spider;

use League\Pipeline\PipelineBuilderInterface;
use Raven\Content\Article\Article;
use Raven\Content\Article\ArticlePipeline;
use Raven\Content\Media\Media;
use Raven\Content\Media\Pipeline\MediaDownloaderPipeline;
use Raven\Core\Http\Request;
use Raven\Core\Http\Response;
use Raven\Core\Parse\DomCrawler;
use Raven\Core\Spider\PaginatedSpider;
use Raven\Pipeline\EloquentPersistencePipeline;
use Raven\Pipeline\TelegramPublisherPipeline;

class CommonSpider extends PaginatedSpider
{
    /**
     * @inheritDoc
     */
    protected function getSinglePageAnchor()
    {
        return '.main-content .item-list-row .col-md-4 a';
    }

    /**
     * @inheritDoc
     */
    protected function getNextPageAnchor()
    {
        return 'ul.pagination li:last-child a';
    }

    /**
     * @inheritDoc
     */
    protected function getIdentity($link)
    {
        return preg_match('/(?<=\/)\d{5,6}(?=\/)/',$link, $matches) ? $matches[0] : $link;
    }


    /**
     * @inheritDoc
     */
    public function parseSingle(
      DomCrawler $crawler,
      Response $response,
      Request $request
    ) {
        $article = new Article($this->getIdentity((string)$request->getUri()));
        $article->title = $crawler->filter('h1 a')->text();
        $article->lead = $crawler->filter('.article-summery p')->text();

        $mainMedia = new Media([
          'original_url' => $crawler->filter('img.cover')->attr('src'),
          'is_main' => 1,
        ]);
        $article->medias->add($mainMedia);
        yield $article;
    }

    /**
     * @inheritDoc
     */
    public function buildPipeline(PipelineBuilderInterface $builder)
    {
        $builder
          ->add(new ArticlePipeline())
          ->add(new MediaDownloaderPipeline())
          ->add(new EloquentPersistencePipeline())
          ->add(new TelegramPublisherPipeline());
    }

}