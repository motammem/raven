<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Pipeline;

use League\Pipeline\StageInterface;
use Raven\Core\Infrastructure\Model;
use Illuminate\Database\QueryException;

class EloquentPersistencePipeline implements StageInterface
{
    public function __invoke($payload)
    {
        if ($payload instanceof Model) {
            try {
                $payload->push();
            } catch (QueryException $e) {
                // just ignore duplicate entered content
            }
        }

        return $payload;
    }
}
