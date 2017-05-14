<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Category;

use Raven\Content\Article\Article;
use Raven\Core\Infrastructure\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Tag.
 *
 * @property string $name Name of the tag
 * @property Article[]|Collection $articles Articles related with tag
 */
class Tag extends Model
{
    protected $table = 'tag';

    /**
     * Article relationship.
     */
    public function articles()
    {
        return $this->morphedByMany(Article::class, 'taggable');
    }
}
