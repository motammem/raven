<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Schedule;

use Raven\Core\Spider\Spider;

/**
 * Interface SchedulerInterface.
 */
interface SchedulerInterface
{
    /**
     * @return Spider[]
     */
    public function getSpiders();
}
