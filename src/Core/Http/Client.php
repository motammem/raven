<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Core\Http;

class Client extends \GuzzleHttp\Client
{
    public function request($method, $uri = '', array $options = [])
    {
        $response =  parent::request($method, $uri, $options); // TODO: Change the autogenerated stub
        return new Response($response->getStatusCode(), $response->getHeaders(), $response->getBody(),
            $response->getProtocolVersion(), $response->getReasonPhrase());
    }

}