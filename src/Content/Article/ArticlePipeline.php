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

use League\Pipeline\StageInterface;

class ArticlePipeline implements StageInterface
{
    public function __invoke($payload)
    {
        if ($payload instanceof Article) {
            $payload->title = trim($payload->title);
            $payload->title = trim($payload->title);
            $payload->lead = trim($payload->lead);
            $payload->pre_title = trim($payload->pre_title);
            $payload->post_title = trim($payload->post_title);
            $payload->body = trim($payload->body);
            $payload->html = trim($payload->html);
        }

        return $payload;
    }
}
