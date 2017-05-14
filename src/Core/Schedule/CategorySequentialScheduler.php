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
          ->where('is_active', '=', '1')
          ->orderBy('last_run')
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
            dd($this->categories->toArray());
            $spiderClass = $crawlableCategory->spider->class;
            /** @var \Raven\Core\Spider\PaginatedSpider $spider */
            $spider = new $spiderClass();
            $spider->setContext(
              [
                'source' => $crawlableCategory->source->name,
                'category' => $crawlableCategory->name,
                'spider' => $crawlableCategory->spider->name,
                'category_id' => $crawlableCategory->id,
              ]
            );
            $spider->addStartUrl($crawlableCategory->url);
            $spiders[] = $spider;
        }

        return $spiders;
    }
}
