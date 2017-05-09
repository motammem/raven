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

use Raven\Core\Http\Request;
use Raven\Core\Http\Response;
use Raven\Core\ExtensionBuilder;
use Raven\Core\Parse\DomCrawler;
use League\Pipeline\PipelineBuilderInterface;
use Raven\Core\Extension\History\HistoryExtension;

abstract class Spider
{
    // fixme: replace context with logger
    private $context = [];

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

    public function buildExtensions(ExtensionBuilder $builder)
    {
        $builder->add(HistoryExtension::class);
    }

    /**
     * @param array $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
