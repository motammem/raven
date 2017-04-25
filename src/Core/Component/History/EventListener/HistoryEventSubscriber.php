<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Component\History\EventListener;

use Carbon\Carbon;
use Raven\Core\Event\Events;
use Raven\Core\Http\Request;
use Raven\Core\Event\ItemEvent;
use Raven\Core\Event\RequestEvent;
use Raven\Core\Component\History\History;
use Raven\Core\Exception\SpiderCloseException;
use Raven\Core\Exception\IgnoreRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HistoryEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::ITEM_SCRAPED => 'itemScraped',
            Events::ON_REQUEST => 'onRequest',
        ];
    }

    public function itemScraped(ItemEvent $event)
    {
        if ( ! ($event->getItem() instanceof Request)) {
            $history = new History([
                'hash' => sha1($event->getRequest()->getUri()),
                'url' => $event->getRequest()->getUri(),
                'visited_at' => Carbon::now(),
            ]);
            try {
                $history->save();
            } catch (\Exception $e) {
            }
        }
    }

    public function onRequest(RequestEvent $event)
    {
        $isCrawledBefore = History::query()->where('hash', '=', sha1($event->getRequest()->getUri()))->count() > 0;
        if (getenv('CRAWL_STRATEGY_DEEP') != 'enable') {
            throw new SpiderCloseException('Reached duplicate item url');
        }
        if ($isCrawledBefore) {
            throw new IgnoreRequestException();
        }
    }
}
