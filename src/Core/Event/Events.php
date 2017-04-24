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

class Events
{
    const SPIDER_CLOSED = 'spider.closed';
    const SPIDER_OPENED = 'spider.opened';
    const ITEM_SCRAPED = 'item.scraped';
    const ON_REQUEST = 'on.request';
    const ON_RESPONSE = 'on.response';
}
