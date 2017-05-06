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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExtensionBuilder
{
    /**
     * @var ArrayCollection|ExtensionInterface[]
     */
    private $extensions;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->extensions = new ArrayCollection();
    }

    public function add($extension)
    {
        $this->extensions->add($extension);
    }

    public function build(EventDispatcherInterface $dispatcher)
    {
        foreach ($this->extensions as $extension) {
            /** @var ExtensionInterface $extensionObject */
            $extensionObject = new $extension();
            $extensionObject->build($dispatcher);
        }
    }
}