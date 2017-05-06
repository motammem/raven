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

use Purl\Url;

class Request extends \GuzzleHttp\Psr7\Request
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $identity;

    /**
     * Request constructor.
     *
     * @param string $uri
     * @param callable $callback
     * @param string $identity
     * @param string $method
     * @param array $headers
     * @param null $body
     * @param string $version
     */
    public function __construct(
        $uri,
        $callback,
        $identity = null,
        $method = 'GET',
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        $this->callback = $callback;
        parent::__construct($method, $uri, $headers, $body, $version);
        $this->identity = $identity;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Resource identity for cases may url is not unique
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return bool
     */
    public function hasIdentity()
    {
        return isset($this->identity);
    }

    public function joinUrl($link)
    {
        $url = Url::parse($link);
        if (!$url->host) {
            return "http://" . $this->getUri()->getHost() . '/' . ltrim($link, '/');
        }
        return $link;
    }
}
