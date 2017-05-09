<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Extension\History\EventListener;

use Carbon\Carbon;
use Raven\Core\Event\Events;
use Raven\Core\Http\Request;
use Raven\Core\Event\ItemEvent;
use Raven\Core\Event\RequestEvent;
use Raven\Core\Extension\History\History;
use Raven\Core\Exception\SpiderCloseException;
use Raven\Core\Exception\IgnoreRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Raven\Core\Extension\History\IdentityGuesser\IdentityGuesserInterface;

class HistoryEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var IdentityGuesserInterface
     */
    private $identityGuesser;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::ITEM_SCRAPED => 'itemScraped',
            Events::ON_REQUEST => 'onRequest',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(IdentityGuesserInterface $identityGuesser)
    {
        $this->identityGuesser = $identityGuesser;
    }

    /**
     * {@inheritdoc}
     */
    public function itemScraped(ItemEvent $event)
    {
        if ( ! ($event->getItem() instanceof Request)) {
            $identity = $this->identityGuesser->guess($event->getRequest());
            $history = new History([
                'hash' => sha1($identity),
                'url' => $event->getRequest()->getUri(),
                'visited_at' => Carbon::now(),
            ]);
            try {
                $history->save();
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onRequest(RequestEvent $event)
    {
        if (getenv('CRAWL_STRATEGY_DEEP') != 'enable') {
            throw new SpiderCloseException('Reached duplicate item url');
        }

        $identity = $this->identityGuesser->guess($event->getRequest());
        if (History::hasNode($identity)) {
            throw new IgnoreRequestException();
        }
    }
}
