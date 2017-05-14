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

use Carbon\Carbon;
use League\Pipeline\StageInterface;

class ArticleTrimPipeline implements StageInterface
{
    /**
     * @param \Raven\Content\Article\Article $payload
     *
     * @return mixed
     */
    public function __invoke($payload)
    {
        $payload->title = trim($payload->title);
        $payload->lead = trim($payload->lead);
        $payload->pre_title = trim($payload->pre_title);
        $payload->post_title = trim($payload->post_title);
        $payload->text = trim($payload->text);
        $payload->html = trim($payload->html);
        $payload->document = trim($payload->document);
        $payload->target_site_id = trim($payload->target_site_id);
        $payload->url = trim($payload->url);

        $payload->created_at = Carbon::now();

        return $payload;
    }
}
