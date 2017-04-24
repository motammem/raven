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

use Raven\Core\Http\Request;
use Symfony\Component\EventDispatcher\Event;

class ItemEvent extends Event
{
    /**
     * @var Request|mixed
     */
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * @return Request|mixed
     */
    public function getItem()
    {
        return $this->item;
    }
}
