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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Raven\Category\CrawlableCategory;
use Raven\Content\Media\Media;
use Raven\Core\Infrastructure\Identity;
use Raven\Core\Infrastructure\Model;

/**
 * Class Article.
 *
 * @property string $id Identity of the article in our system
 * @property string $title Title of the article
 * @property string $lead Text brief of article
 * @property string $pre_title Comes before title
 * @property string $post_title Comes after title
 * @property string $body Text section of the html
 * @property string $html Html section of the content this part includes images and videos
 * @property string $document Whole page article belongs to
 * @property string $target_site_id Identity of the article in target website
 * @property string $url Url of the article source
 * @property Media[] $medias Medias attached to article
 * @property CrawlableCategory $category Category article belongs to
 * @property Carbon $created_at When article created in our site
 * @property Carbon $publish_date When article published in target site
 */
class Article extends Model
{

    use Identity;

    protected $table = 'article';

    public $timestamps = false;

    public function medias()
    {
        return $this->morphMany(Media::class, 'content');
    }

    public function mainMedia()
    {
        foreach ($this->medias as $media) {
            if ($media->is_main == 1) {
                return $media;
            }
        }

        return new Media();
    }

    public function category()
    {
        return $this->belongsTo(CrawlableCategory::class, 'category_id', 'id');
    }
}
