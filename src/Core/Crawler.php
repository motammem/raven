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

use Monolog\Logger;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Raven\Core\Event\Events;
use Raven\Core\Http\Request;
use League\Pipeline\Pipeline;
use Raven\Core\Event\ItemEvent;
use Raven\Core\Event\SpiderEvent;
use Raven\Core\Event\RequestEvent;
use Raven\Core\Event\ResponseEvent;
use League\Pipeline\PipelineBuilder;
use Raven\Core\Exception\SpiderCloseException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Crawler
{
    /**
     * @var ArrayCollection|Spider[]
     */
    private $spiders;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(Client $client, EventDispatcher $dispatcher, LoggerInterface $logger = null)
    {
        $this->spiders = new ArrayCollection();
        $this->client = $client;
        if ( ! $logger) {
            $logger = new Logger('crawler');
        }
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Start crawling spiders.
     */
    public function start()
    {
        foreach ($this->spiders as $spider) {
            try {
                // build spider pipeline
                $builder = new PipelineBuilder();
                $spider->buildPipeline($builder);
                /** @var Pipeline $pipeline */
                $pipeline = $builder->build();

                $this->dispatcher->dispatch(Events::SPIDER_OPENED, new SpiderEvent($spider));

                foreach ($spider->startRequests() as $request) {
                    foreach ($this->handleRequest($request) as $items) {
                        foreach ($items as $item) {
                            $this->dispatcher->dispatch(Events::ITEM_SCRAPED, new ItemEvent($item));
                            $pipeline->process($item);
                        }
                    }
                }
            } catch (SpiderCloseException $e) {
                $this->logger->info('Spider closed cause '.strtolower($e->getCause()), $e->getContext());
            }
            $this->dispatcher->dispatch(Events::SPIDER_CLOSED, new SpiderEvent($spider));
        }
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Generator
     */
    public function handleRequest(Request $request)
    {
        $this->dispatcher->dispatch(Events::ON_REQUEST, new RequestEvent($request));

        $this->logger->info('Requesting', [
            'url' => (string) $request->getUri(),
        ]);

        // perform request
        $response = $this->client->request($request->getMethod(), $request->getUri());
        $this->dispatcher->dispatch(Events::ON_RESPONSE, new ResponseEvent($response));

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
     * @return Spider[]|ArrayCollection
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

    /**
     * @param Spider $spider
     */
    public function addSpider(Spider $spider)
    {
        $this->spiders->add($spider);
    }
}
