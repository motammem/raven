<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface MiddlewareInterface.
 */
interface ExtensionInterface
{
    public static function getName();

    public static function build(EventDispatcherInterface $dispatcher);
}
