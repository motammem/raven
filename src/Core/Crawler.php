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
use Raven\Core\Event\Events;
use Raven\Core\Http\Request;
use League\Pipeline\Pipeline;
use Raven\Core\Spider\Spider;
use Raven\Core\Event\ItemEvent;
use Raven\Core\Parse\DomCrawler;
use Raven\Core\Event\SpiderEvent;
use Raven\Core\Event\RequestEvent;
use Raven\Core\Event\ResponseEvent;
use League\Pipeline\PipelineBuilder;
use GuzzleHttp\Exception\RequestException;
use Raven\Core\Exception\SpiderCloseException;
use Raven\Core\Exception\IgnoreRequestException;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Crawler
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(Client $client, EventDispatcher $dispatcher)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Start crawling spiders.
     *
     * @param \Raven\Core\Spider\Spider $spider
     */
    public function start(Spider $spider)
    {
        // create log context including source, category and spider name
        try {
            /** @var \Raven\Core\Spider\Spider $spider */
            $extensionBuilder = new ExtensionBuilder();
            $spider->buildExtensions($extensionBuilder);
            $extensionBuilder->build($this->dispatcher);

            // build spider pipeline
            /** @var Pipeline $pipeline */
            $builder = new PipelineBuilder();
            $spider->buildPipeline($builder);
            $pipeline = $builder->build();

            // dispatch spider.open event
            $this->dispatcher->dispatch(Events::SPIDER_OPENED, new SpiderEvent($spider));

            // use spider
            foreach ($spider->startRequests() as $request) {
                foreach ($this->handleRequest($request) as $item) {
                    _log(Logger::INFO, 'Piping item');
                    $pipeline->process($item);
                }
            }

            // spider done it's job successfully
            _log(Logger::INFO, 'Spider finished successfully');
        } catch (SpiderCloseException $e) {
            // close spider and ignore it
            _log(Logger::INFO, 'Spider closed cause '.strtolower($e->getCause()));
        }
        // dispatch spider.close event
        $this->dispatcher->dispatch(Events::SPIDER_CLOSED, new SpiderEvent($spider));
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
        // check for IgnoreRequestException
        try {
            /** @var Request[] $requestsToPerform */
            $requestsToPerform = [$request];
            while (count($requestsToPerform) > 0) {
                $request = array_shift($requestsToPerform);
                try {
                    $this->dispatcher->dispatch(Events::ON_REQUEST, new RequestEvent($request));
                } catch (IgnoreRequestException $e) {
                    _log(Logger::INFO, 'ignoring request', [(string) $request->getUri()]);
                    continue;
                }
                _log(Logger::INFO, 'Requesting', ['url' => (string) $request->getUri(), 'agent' => $request->getHeaders()]);
                // perform request
                $response = $this->client->request($request->getMethod(), $request->getUri());
                $this->dispatcher->dispatch(Events::ON_RESPONSE, new ResponseEvent($response));
                // process callback results
                $crawler = new DomCrawler($response->getBody()->getContents());
                $results = call_user_func($request->getCallback(), $crawler, $response, $request);
                foreach ($results as $result) {
                    $this->dispatcher->dispatch(Events::ITEM_SCRAPED, new ItemEvent($result, $request));
                    if ($result instanceof Request) {
                        $requestsToPerform[] = $result;
                    } else {
                        yield $result;
                    }
                }
            }
            // just ignore request
        } catch (\InvalidArgumentException $e) {
            // parse errors
            $trace = $e->getTrace();
            $logContext['file'] = $trace[0]['file'];
            $logContext['line'] = $trace[0]['line'];
            $uri = $trace[1]['args'][2]->getUri();
            $logContext['url'] = (string) $uri;
            _log(Logger::ERROR, 'Parser not matched', $logContext);
        } catch (RequestException $e) {
            // connection errors
            $logContext['url'] = $e->getRequest()->getUri();
            _log(Logger::ERROR, 'Connection timeout', $logContext);
        }
    }
}
