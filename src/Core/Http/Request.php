<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Http;

class Request extends \GuzzleHttp\Psr7\Request
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Request constructor.
     *
     * @param string   $uri
     * @param callable $callback
     * @param string   $method
     * @param array    $headers
     * @param null     $body
     * @param string   $version
     */
    public function __construct($uri, $callback, $method = 'GET', array $headers = [], $body = null, $version = '1.1')
    {
        $this->callback = $callback;
        parent::__construct($method, $uri, $headers, $body, $version);
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
