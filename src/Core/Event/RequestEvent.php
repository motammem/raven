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

class RequestEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Raven\Core\Http\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
