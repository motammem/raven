<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Content\Media\Pipeline;

use Raven\Core\DomCrawler;
use Raven\Content\Media\Media;
use Raven\Content\Article\Article;
use League\Pipeline\StageInterface;

class MediaExtractorPipeline implements StageInterface
{
    /**
     * @param Article $article
     *
     * @return Article
     */
    public function __invoke($article)
    {
        $crawler = new DomCrawler($article->body);
        $imageSources = $crawler->filter('img')->attr('src');
        $article->medias->add(new Media([
            'original_url' => $imageSources,
        ]));

        return $article;
    }
}
