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

use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineBuilder;
use Psr\Log\LoggerInterface;
use Raven\Category\CrawlableCategory;
use Raven\Core\Event\Events;
use Raven\Core\Event\ItemEvent;
use Raven\Core\Event\RequestEvent;
use Raven\Core\Event\ResponseEvent;
use Raven\Core\Event\SpiderEvent;
use Raven\Core\Exception\CrawlerException;
use Raven\Core\Exception\IgnoreRequestException;
use Raven\Core\Exception\SpiderCloseException;
use Raven\Core\Http\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CategoryCrawler extends Crawler
{
    /**
     * @var ArrayCollection|CrawlableCategory[]
     */
    private $categories;

    public function __construct(Client $client, EventDispatcher $dispatcher, LoggerInterface $logger = null)
    {
        parent::__construct($client, $dispatcher, $logger);
        $this->categories = new ArrayCollection();
    }

    /**
     * Start crawling spiders.
     */
    public function start()
    {
        // for each given category begin crawl process
        foreach ($this->categories as $category) {
            // create log context including source, category and spider name
            $logContext = $this->generateLogContext($category);

            try {
                // build spider
                /** @var Spider $spider */
                $spider = $category->spider();
                $spider = new $spider();

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
                        $this->logger->info('Piping item', ['item' => $item]);
                        $pipeline->process($item);
                    }
                }

                // spider done it's job successfully
                $this->logger->info('Spider finished successfully', $logContext);
            } catch (SpiderCloseException $e) {
                // close spider and ignore it
                $this->logger->info('Spider closed cause ' . strtolower($e->getCause()), $logContext);
            } catch (\InvalidArgumentException $e) {
                // parse errors
                $trace = $e->getTrace();
                $logContext['file'] = $trace[0]['file'];
                $logContext['line'] = $trace[0]['line'];
                throw new CrawlerException($logContext, 'Parser not matched:' . $e->getMessage(), 0, $e);
            } catch (RequestException $e) {
                // connection errors
                $logContext['url'] = $e->getRequest()->getUri();
                throw new CrawlerException($logContext, 'Connection timeout: ' . $e->getMessage(), 0, $e);
            }

            // dispatch spider.close event
            $this->dispatcher->dispatch(Events::SPIDER_CLOSED, new SpiderEvent($spider));
        }
    }

    /**
     * @param $category CrawlableCategory
     *
     * @return array
     */
    public function generateLogContext($category)
    {
        $matches = [];
        preg_match('/(?<=Source\\\\).*?(?=\\\\)/', get_class($category), $matches);
        $source = strtolower($matches[0]);
        preg_match('/(?<=\\\\)[^\\\\]+?(?=Spider$)/', $category->spider(), $matches);
        $spider = strtolower($matches[0]);
        $logContext = [
            'source' => $source,
            'category' => $category->getName(),
            'spider' => $spider,
        ];

        return $logContext;
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
            $this->dispatcher->dispatch(Events::ON_REQUEST, new RequestEvent($request));
            $this->logger->info('Requesting', [
                'url' => (string)$request->getUri(),
            ]);
            // perform request
            $response = $this->client->request($request->getMethod(), $request->getUri());
            $this->dispatcher->dispatch(Events::ON_RESPONSE, new ResponseEvent($response));

            // process callback results
            $crawler = new DomCrawler($response->getBody()->getContents());
            $results = call_user_func($request->getCallback(), $crawler, $response, $request);
            foreach ($results as $result) {
                $this->dispatcher->dispatch(Events::ITEM_SCRAPED, new ItemEvent($result, $request));
                if ($result instanceof Request) {
                    foreach ($this->handleRequest($result) as $item) {
                        yield $item;
                    }
                } else {
                    yield $result;
                }
            }
        } catch (IgnoreRequestException $e) {
            // just ignore request
        }
    }

    /**
     * @return CrawlableCategory[]|ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param CrawlableCategory[] $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param CrawlableCategory $category
     */
    public function addCategory(CrawlableCategory $category)
    {
        $this->categories->add($category);
    }
}
