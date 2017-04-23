<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Core;

use GuzzleHttp\Psr7\Response;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Log\LoggerInterface;
use Raven\Core\Http\Request;

abstract class Spider
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return \Generator|Request[]
     */
    public function startRequests()
    {
        foreach ($this->getStartUrls() as $url) {
            yield new Request($url, [$this, 'parse']);
        }
    }

    /**
     * @return string[]
     */
    abstract public function getStartUrls();

    /**
     * @param DomCrawler $crawler
     * @param Response $response
     * @param Request $request
     * @return mixed|Request[]
     */
    abstract public function parse(DomCrawler $crawler, Response $response, Request $request);

    /**
     * @param PipelineBuilderInterface $builder
     * @return void
     */
    abstract public function buildPipeline(PipelineBuilderInterface $builder);

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
