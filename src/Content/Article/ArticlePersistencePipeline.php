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

use Illuminate\Database\QueryException;
use League\Pipeline\StageInterface;
use Raven\Category\Tag;

class ArticlePersistencePipeline implements StageInterface
{

    /**
     * @param \Raven\Content\Article\Article $article
     *
     * @return mixed
     */
    public function __invoke($article)
    {
        global $capsule;
        $capsule->getConnection()->beginTransaction();

        $article->save();

        // save tags with respect to their uniqueness
        foreach ($article->tags as $tag) {
            $tag = Tag::query()->firstOrCreate([
              'name' => $tag->name
            ]);
            $article->tags()->attach($tag);
        }

        $article->medias()->saveMany($article->medias);

        $capsule->getConnection()->commit();

        return $article;
    }

}