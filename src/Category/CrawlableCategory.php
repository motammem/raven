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

/**
 * Class CrawlableCategory.
 */
abstract class CrawlableCategory
{
    /**
     * @return string Fully qualified url of the category
     */
    abstract public function url();

    /**
     * @return string Unique category name in related source
     */
    abstract public function getName();

    /**
     * @return string Fully qualified parent category class in related source
     */
    abstract public function parent();

    /**
     * @return bool determines if category is active for crawling
     */
    abstract public function isActive();

    /**
     * @return string Hierarchy of the equivalent category separated by dot
     */
    abstract public function map();

    /**
     * @return string Fully qualified class name of the related spider
     */
    abstract public function spider();
}
