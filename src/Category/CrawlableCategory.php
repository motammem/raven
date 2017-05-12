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
 * Class CrawlableCategory.
 */
class CrawlableCategory extends Model
{
    protected $table = 'crawlable_category';

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'system_category_id', 'id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    public function spider()
    {
        return $this->belongsTo(Spider::class, 'spider_id', 'id');
    }
}
