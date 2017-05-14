<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Source\Zoomit\Spider;

use Carbon\Carbon;
use Raven\Core\Http\Request;
use Raven\Core\Http\Response;
use Raven\Content\Media\Media;
use Raven\Core\Parse\DomCrawler;
use Raven\Content\Article\Article;
use Raven\Core\Spider\PaginatedSpider;
use Raven\Content\Article\ArticlePipeline;
use League\Pipeline\PipelineBuilderInterface;
use Raven\Pipeline\TelegramPublisherPipeline;
use Raven\Pipeline\EloquentPersistencePipeline;
use Raven\Content\Media\Pipeline\MediaDownloaderPipeline;

class CommonSpider extends PaginatedSpider
{
    /**
     * {@inheritdoc}
     */
    protected function getSinglePageAnchor()
    {
        return '.main-content .item-list-row .col-md-4 a';
    }

    /**
     * {@inheritdoc}
     */
    protected function getNextPageAnchor()
    {
        return 'ul.pagination li:last-child a';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdentity($link)
    {
        return preg_match('/(?<=\/)\d{5,6}(?=\/)/', $link, $matches) ? $matches[0] : $link;
    }

    /**
     * {@inheritdoc}
     */
    public function parseSingle(DomCrawler $crawler, Response $response, Request $request)
    {
        $article = new Article($this->getIdentity((string) $request->getUri()));
        $article->title = $crawler->filter('h1 a')->text();
        $article->lead = $crawler->filter('.article-summery p')->text();
        $article->pre_title = null;
        $article->post_title = null;
        $section = $crawler->filter('.article-section');
        $article->body = $section->text();
        $article->html = $section->html();
        $article->document = $crawler->html();
        // $article->target_site_id = null; this filled with identity
        $article->url = $request->getUri();
        $article->category = null;
        $article->created_at = Carbon::now();
        $article->publish_date = null;
        $mainMedia = new Media([
          'original_url' => $crawler->filter('img.cover')->attr('src'),
          'is_main' => 1,
        ]);
        $article->medias[] = $mainMedia;
        yield $article;
    }

    /**
     * {@inheritdoc}
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
