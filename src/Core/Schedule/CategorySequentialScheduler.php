<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Schedule;

use Raven\Category\CrawlableCategory;
use Raven\Source\KhabarOnline\Category\Culture;

class CategorySequentialScheduler implements SchedulerInterface
{
    /**
     * @var CrawlableCategory[]
     */
    private $categories = [];

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->categories = [
          Culture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSpiders()
    {
        $spiders = [];
        foreach ($this->categories as $categoryClass) {
            $category = new $categoryClass();
            /** @var \Raven\Core\Spider\Spider $spider */
            $spiderClass = $category->spider();
            $spider = new $spiderClass();
            $matches = [];
            preg_match('/(?<=Source\\\\).*?(?=\\\\)/', get_class($category), $matches);
            $source = strtolower($matches[0]);
            preg_match('/(?<=\\\\)[^\\\\]+?(?=Spider$)/', get_class($spider), $matches);
            $spiderName = strtolower($matches[0]);
            $spider->setContext([
              'source' => $source,
              'category' => $category->getName(),
              'spider' => $spiderName,
            ]);
            $spiders[] = $spider;
        }

        return $spiders;
    }
}
