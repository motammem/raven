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

use Raven\Content\Media\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Article.
 *
 * @property MorphMany $medias
 */
class Article extends Model
{
    protected $table = 'article';
    protected $fillable = [
        'title',
        'lead',
        'pre_title',
        'post_title',
        'body',
    ];

    /**
     * @return MorphMany
     */
    public function medias()
    {
        return $this->morphMany(Media::class, 'content');
    }

    public function mainMedia()
    {
        return $this->medias()->where('is_main', '=', '1');
    }
}
