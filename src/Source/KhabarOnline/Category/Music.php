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

class Music extends CrawlableCategory
{
    /**
     * {@inheritdoc}
     */
    public function url()
    {
        return 'http://www.khabaronline.ir/list/culture/music';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'music';
    }

    /**
     * {@inheritdoc}
     */
    public function parent()
    {
        return Culture::class;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function map()
    {
        // TODO: Implement map() method.
    }

    /**
     * {@inheritdoc}
     */
    public function spider()
    {
        return CommonSpider::class;
    }
}
