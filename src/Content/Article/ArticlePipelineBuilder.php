<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Content\Article;

use League\Pipeline\PipelineBuilderInterface;
use Raven\Content\Media\Pipeline\MediaDownloaderPipeline;
use Raven\Pipeline\TelegramPublisherPipeline;

trait ArticlePipelineBuilder
{

    public function buildPipeline(PipelineBuilderInterface $builder)
    {
        $builder
          ->add(new ArticleTrimPipeline())
          ->add(new MediaDownloaderPipeline())
          ->add(new ArticlePersistencePipeline())
          ->add(new TelegramPublisherPipeline())
        ;
    }
}
