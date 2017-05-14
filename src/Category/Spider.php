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

use Raven\Core\Infrastructure\Model;

/**
 * Class Spider.
 *
 * @property string $name Name of the spider
 * @property string $class Class of the spider
 * @property Source $source Source of the spider
 */
class Spider extends Model
{
    protected $table = 'spider';

    /**
     * Source relationship.
     */
    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }
}
