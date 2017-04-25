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

use Raven\Core\Identity;
use Raven\Content\Media\Media;
use Raven\Infrastructure\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Article.
 *
 * @property MorphMany $medias
 */
class Article extends Model
{
    use Identity;

    protected $table = 'article';
    protected $fillable = [
        'title',
        'lead',
        'pre_title',
        'post_title',
        'target_site_id',
        'url',
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
        return $this->medias()->where('is_main', '=', '1')->first();
    }
}
