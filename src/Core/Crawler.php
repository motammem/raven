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

use GuzzleHttp\Client;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineBuilder;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Raven\Core\Exception\SpiderCloseException;
use Raven\Core\Http\Request;

class Crawler
{
    /**
     * @var Spider[]
     */
    private $spiders = [];

    /**
     * @var Client
     */
    private $client;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Client $client, LoggerInterface $logger = null)
    {
        $this->client = $client;
        if (!$logger) {
            $logger = new Logger('crawler');
        }
        $this->logger = $logger;
    }


    public function start()
    {
        foreach ($this->spiders as $spider) {
            try {

                // build spider pipeline
                $builder = new PipelineBuilder();
                $spider->buildPipeline($builder);
                /** @var Pipeline $pipeline */
                $pipeline = $builder->build();

                foreach ($spider->startRequests() as $request) {
                    foreach ($this->handleRequest($request) as $items) {
                        foreach ($items as $bang) {
                            $pipeline->process($bang);
                        }
                    }
                }
            } catch (SpiderCloseException $e) {
                $this->logger->info('Spider closed cause ' . strtolower($e->getCause()), $e->getContext());
            }
        }
    }

    /**
     * @param Request $request
     * @return \Generator
     * @throws \Exception
     */
    public function handleRequest(Request $request)
    {
        $this->logger->info(sprintf("Requesting"), [
            'url' => (string)$request->getUri()
        ]);

        try {
            // perform request
            $response = $this->client->request($request->getMethod(), $request->getUri());
        } catch (\Exception $e) {
            $this->logger->crit("Request error", [
                'url' => $request->getUri(),
            ]);
            throw $e;
        }

        // process callback results
        $crawler = new DomCrawler($response->getBody()->getContents());
        $results = call_user_func($request->getCallback(), $crawler, $response, $request);
        foreach ($results as $result) {
            if ($result instanceof Request) {
                yield $this->handleRequest($result);
            } else {
                yield $result;
            }
        }
    }

    /**
     * @return Spider[]
     */
    public function getSpiders()
    {
        return $this->spiders;
    }

    /**
     * @param Spider[] $spiders
     */
    public function setSpiders($spiders)
    {
        $this->spiders = $spiders;
    }
}
