<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Event;

use Raven\Core\Spider\Spider;
use Symfony\Component\EventDispatcher\Event;

class SpiderEvent extends Event
{
    /**
     * @var Spider
     */
    private $spider;

    public function __construct(Spider $spider)
    {
        $this->spider = $spider;
    }

    /**
     * @return Spider
     */
    public function getSpider()
    {
        return $this->spider;
    }
}
