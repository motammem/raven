<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Raven\Functional;

use PHPUnit\Framework\TestCase;
use Raven\Core\Schedule\CategorySequentialScheduler;

class LogTest extends TestCase
{
    public function testLog()
    {
        $scheulder = new CategorySequentialScheduler();
    }
}
