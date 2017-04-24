<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Content\Media\Pipeline;

use League\Pipeline\StageInterface;
use Raven\Content\Article\Article;
use Raven\Content\Media\Media;
use Raven\Core\DomCrawler;

class MediaExtractorPipeline implements StageInterface
{
    /**
     * @param Article $article
     * @return Article
     */
    public function __invoke($article)
    {
        $crawler = new DomCrawler($article->body);
        $imageSources = $crawler->filter('img')->attr('src');
        $article->medias->add(new Media([
            'original_url' => $imageSources
        ]));
        return $article;
    }

}