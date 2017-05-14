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

use Raven\Category\Tag;
use Raven\Content\Article\Article;
use Raven\Content\Article\ArticlePipelineBuilder;
use Raven\Content\Media\Media;
use Raven\Core\Http\Request;
use Raven\Core\Http\Response;
use Raven\Core\Parse\DomCrawler;
use Raven\Core\Spider\PaginatedSpider;
use Symfony\Component\DomCrawler\Crawler;

class CommonSpider extends PaginatedSpider
{

    use ArticlePipelineBuilder;

    /**
     * {@inheritdoc}
     */
    public function parseSingle(
      DomCrawler $crawler,
      Response $response,
      Request $request
    ) {
        $article = new Article($this->getIdentity((string)$request->getUri()));
        $article->title = $crawler->filter('h1 a')->text();
        $article->lead = $crawler->filter('.article-summery p')->text();
        $article->pre_title = null;
        $article->post_title = null;
        $section = $crawler->filter('.article-section');
        $article->text = $section->text();
        $article->html = $section->html();
        $article->document = $crawler->html();
        // $article->target_site_id = null; this filled with identity
        $article->url = (string)$request->getUri();
        $article->category_id = $this->getContext('category_id');
        $article->published_at_label = $crawler->filter('.author-details-row2')->text();

        $crawler->filter('.article-tag-row a')->each(
          function (Crawler $crawler) use ($article) {
              $article->tags->push(new Tag(['name' => $crawler->text()]));
          }
        );

        $mainMedia = new Media([
            'original_url' => $crawler->filter('img.cover')->attr('src'),
            'is_main' => 1,
        ]);
        $article->medias->push($mainMedia);
        yield $article;
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdentity($link)
    {
        return preg_match(
          '/(?<=\/)\d{5,6}(?=\/)/',
          $link,
          $matches
        ) ? $matches[0] : $link;
    }

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
}
