<?php

/*
 *
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Raven\Core\Pipeline;

use League\Pipeline\StageInterface;

class EchoStage implements StageInterface
{
    public function __invoke($payload)
    {
        var_dump($payload);
    }
}
