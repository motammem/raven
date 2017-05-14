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
        $this->categories = CrawlableCategory::query()
          ->orderBy('last_run', 'desc')
          ->where('is_active', '=', '1')
          ->limit(2)
          ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSpiders()
    {
        $spiders = [];
        foreach ($this->categories as $crawlableCategory) {
            $crawlableCategory->last_run = new \DateTime();
            $crawlableCategory->save();
            $spiderClass = $crawlableCategory->spider->class;
            /** @var \Raven\Core\Spider\PaginatedSpider $spider */
            $spider = new $spiderClass();
            $spider->setContext(
              [
                'source' => $crawlableCategory->source->name,
                'category' => $crawlableCategory->name,
                'spider' => $crawlableCategory->spider->name,
                'category_id' => $crawlableCategory->spider->name,
              ]
            );
            $spider->addStartUrl($crawlableCategory->url);
            $spiders[] = $spider;
        }

        return $spiders;
    }
}
