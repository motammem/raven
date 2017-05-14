<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Content\Media;

use Carbon\Carbon;
use Raven\Core\Infrastructure\Model;

/**
 * Class Media.
 *
 * @property string $id Identity of the media in system
 * @property string $title Title of the media
 * @property string $filename File name of the media
 * @property string $path Absolute path of the media on host
 * @property string $original_url Url of the media in target website
 * @property bool $is_main Determines if this media is main in related article medias
 * @property Carbon $created_at Datetime when media created on our site
 * @property Carbon $published_at Datetime when media published in target site
 */
class Media extends Model
{
    protected static $unguarded = true;

    public $timestamps = false;

    protected $table = 'media';

    protected $attributes = [
      'is_main' => false,
    ];

    protected $casts = [
      'published_at' => 'datetime',
      'created_at' => 'datetime',
      'is_main' => 'boolean',
    ];

    /**
     * Content relationship.
     */
    public function content()
    {
        return $this->morphTo(null, null, 'id');
    }
}
