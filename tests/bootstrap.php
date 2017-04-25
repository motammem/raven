<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../bootstrap.php';
function data($path)
{
    return file_get_contents(__DIR__.'/_data/'.ltrim($path, '/'));
}
