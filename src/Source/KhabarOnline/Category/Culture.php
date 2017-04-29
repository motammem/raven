<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Source\KhabarOnline\Category;

use Raven\Category\CrawlableCategory;
use Raven\Source\KhabarOnline\Spider\CommonSpider;

class Culture extends CrawlableCategory
{
    public function url()
    {
        return 'http://www.khabaronline.ir/service/culture';
    }

    public function getName()
    {
        return 'culture';
    }

    public function parent()
    {
        return null;
    }

    public function isActive()
    {
        return true;
    }

    public function map()
    {
        return 'category';
    }

    public function spider()
    {
        return CommonSpider::class;
    }

    // strategy
    // extensions in use
    // image
    //
}
