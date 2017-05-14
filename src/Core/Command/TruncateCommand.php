<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Command;

use Raven\Category\Tag;
use Raven\Content\Article\Article;
use Raven\Content\Media\Media;
use Raven\Core\Extension\History\History;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TruncateCommand extends Command
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        Article::query()->truncate();
        History::query()->truncate();
        Media::query()->truncate();
        Tag::query()->truncate();
    }

    protected function configure()
    {
        $this->setName('truncate');
    }
}
