<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Extension\UserAgent\EventListener;

use Raven\Core\Event\Events;
use Raven\Core\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserAgentEventSubscriber implements EventSubscriberInterface
{
    private $agents;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->agents = include_once __DIR__.'/../resource/user_agents.php';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
          Events::ON_REQUEST => 'onRequest',
        ];
    }

    public function onRequest(RequestEvent $event)
    {
        $key = array_rand($this->agents);
        $request = $event->getRequest()->withHeader(
          'User-Agent',
          $this->agents[$key]
        );
        $event->setRequest($request);
    }
}
