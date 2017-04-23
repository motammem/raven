<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Raven\Content\Media\Media;

class MediaEmbeddedContent
{
    /**
     * @var Media[]
     */
    protected $medias = [];

    /**
     * MediaEmbeddedContent constructor.
     * @param Media[] $medias
     */
    public function __construct(array $medias = [])
    {
        $this->medias = new ArrayCollection($medias);
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function addMedia(Media $media)
    {
        $this->medias->add($media);
        return $this;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
        return $this;
    }

    /**
     * @return ArrayCollection|Media[]
     */
    public function getMedias()
    {
        return $this->medias;
    }
}