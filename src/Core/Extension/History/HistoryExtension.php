<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Extension\History;

use Raven\Core\ExtensionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Raven\Core\Extension\History\IdentityGuesser\IdentityGuesser;
use Raven\Core\Extension\History\EventListener\HistoryEventSubscriber;

class HistoryExtension implements ExtensionInterface
{
    public static function getName()
    {
        return 'history';
    }

    public static function build(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new HistoryEventSubscriber(new IdentityGuesser()));
    }
}
