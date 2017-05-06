<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Spider;

use League\Pipeline\PipelineBuilderInterface;
use Psr\Log\LoggerInterface;
use Raven\Core\Extension\History\HistoryExtension;
use Raven\Core\ExtensionBuilder;
use Raven\Core\Http\Request;
use Raven\Core\Http\Response;
use Raven\Core\Parse\DomCrawler;

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
     * @param Response   $response
     * @param Request    $request
     *
     * @return mixed|Request[]
     */
    abstract public function parse(DomCrawler $crawler, Response $response, Request $request);

    /**
     * @param PipelineBuilderInterface $builder
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

    public function buildExtensions(ExtensionBuilder $builder)
    {
//        $builder->add(HistoryExtension::class);
    }
}
