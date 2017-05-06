<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Strategy\Category;

use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\RequestException;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineBuilder;
use Psr\Log\LoggerInterface;
use Raven\Category\CrawlableCategory;
use Raven\Core\Crawler;
use Raven\Core\Event\Events;
use Raven\Core\Event\ItemEvent;
use Raven\Core\Event\RequestEvent;
use Raven\Core\Event\ResponseEvent;
use Raven\Core\Event\SpiderEvent;
use Raven\Core\Exception\CrawlerException;
use Raven\Core\Exception\IgnoreRequestException;
use Raven\Core\Exception\SpiderCloseException;
use Raven\Core\ExtensionBuilder;
use Raven\Core\Http\Client;
use Raven\Core\Http\Request;
use Raven\Core\Parse\DomCrawler;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CategoryCrawler extends Crawler
{
    /**
     * @var ArrayCollection|CrawlableCategory[]
     */
    private $categories;

    /**
     * @var CrawlableCategory
     */
    private $category;

    public function __construct(
        Client $client,
        EventDispatcher $dispatcher,
        CrawlableCategory $category,
        LoggerInterface $logger = null
    ) {
        parent::__construct($client, $dispatcher, $logger);
        $this->categories = new ArrayCollection();
        $this->category = $category;
    }

    /**
     * Start crawling spiders.
     */
    public function start()
    {
        // create log context including source, category and spider name
        $logContext = $this->generateLogContext();

        try {
            // build spider
            /** @var \Raven\Core\Spider\Spider $spider */
            $spider = $this->category->spider();
            $spider = new $spider();

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
                    $this->logger->info('Piping item', $this->generateLogContext());
                    $pipeline->process($item);
                }
            }

            // spider done it's job successfully
            $this->logger->info('Spider finished successfully', $logContext);
        } catch (SpiderCloseException $e) {
            // close spider and ignore it
            $this->logger->info('Spider closed cause ' . strtolower($e->getCause()), $logContext);
        } catch (RequestException $e) {
            // connection errors
            $logContext['url'] = $e->getRequest()->getUri();
            throw new CrawlerException($logContext, 'Connection timeout: ' . $e->getMessage(), 0, $e);
        }

        // dispatch spider.close event
        $this->dispatcher->dispatch(Events::SPIDER_CLOSED, new SpiderEvent($spider));
    }

    /**
     * @param array $additional
     * @return array
     */
    public function generateLogContext($additional = array())
    {
        $matches = [];
        preg_match('/(?<=Source\\\\).*?(?=\\\\)/', get_class($this->category), $matches);
        $source = strtolower($matches[0]);
        preg_match('/(?<=\\\\)[^\\\\]+?(?=Spider$)/', $this->category->spider(), $matches);
        $spider = strtolower($matches[0]);
        $logContext = [
            'source' => $source,
            'category' => $this->category->getName(),
            'spider' => $spider,
        ];

        return array_merge($logContext, $additional);
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
            $requestsToPerform = [$request];
            while (count($requestsToPerform) > 0) {
                echo memory_get_usage()."\n";
                $request = array_shift($requestsToPerform);
                $this->dispatcher->dispatch(Events::ON_REQUEST, new RequestEvent($request));
                $this->logger->info('Requesting', $this->generateLogContext(['url' => (string)$request->getUri()]));
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

        } catch (IgnoreRequestException $e) {
            // just ignore request
        } catch (\InvalidArgumentException $e) {
            // parse errors
            $trace = $e->getTrace();
            $logContext['file'] = $trace[0]['file'];
            $logContext['line'] = $trace[0]['line'];
            $uri = $trace[1]['args'][2]->getUri();
            $logContext['url'] = (string)$uri;
            throw new CrawlerException($this->generateLogContext($logContext), 'Parser not matched ' . $e->getMessage(),
                $e->getCode(), $e);
        }
    }
}
