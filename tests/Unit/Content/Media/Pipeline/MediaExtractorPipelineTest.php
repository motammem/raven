<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Raven\Unit\Content\Media\Pipeline;

use Raven\Core\DomCrawler;
use PHPUnit\Framework\TestCase;
use Raven\Content\Article\Article;
use Raven\Content\Media\Pipeline\MediaExtractorPipeline;

class MediaExtractorPipelineTest extends TestCase
{
    public function testInvoke()
    {
        $article = new Article();
        $article->html = data('node.html');
        $article->body = (new DomCrawler($article->html))->filter('p')->html();
        $pipe = new MediaExtractorPipeline();
        $article = $pipe($article);
        $article->save();
    }
}
