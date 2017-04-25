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

use Raven\Infrastructure\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $attributes = [
        'is_main' => 0,
    ];
    protected $fillable = [
        'id',
        'title',
        'filename',
        'path',
        'original_url',
        'content_id',
        'is_main',
    ];

    public function content()
    {
        return $this->morphTo(null, null, 'id');
    }
}
