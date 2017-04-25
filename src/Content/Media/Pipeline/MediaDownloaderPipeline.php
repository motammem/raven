<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Content\Media\Pipeline;

use Raven\Content\Article\Article;
use League\Pipeline\StageInterface;

class MediaDownloaderPipeline implements StageInterface
{
    /**
     * @param Article $article
     *
     * @return Article
     */
    public function __invoke($article)
    {
        $path = root_path(getenv('MEDIA_DIR'));
        if ( ! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        foreach ($article->medias as $media) {
            $url = $media->original_url;
            $filename = basename($url);
            $filePath = $path.'/'.$filename;
            file_put_contents($filePath, file_get_contents($url));
            $media->filename = $filename;
            $media->path = $filePath;
        }

        return $article;
    }
}
