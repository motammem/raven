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

use League\Pipeline\StageInterface;
use Raven\Content\Article\Article;

class MediaDownloaderPipeline implements StageInterface
{
    /**
     * @param Article $article
     * @return Article
     */
    public function __invoke($article)
    {
    }
}
