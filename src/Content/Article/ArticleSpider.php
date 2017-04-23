<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Content\Article;

use League\Pipeline\PipelineBuilderInterface;
use Raven\Core\Spider;

abstract class ArticleSpider extends Spider
{
    public function buildPipeline(PipelineBuilderInterface $builder)
    {
        // TODO: Implement buildPipeline() method.
    }
}
