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

use Raven\Core\Http\Request;
use PHPUnit\Framework\TestCase;
use Raven\Core\Extension\History\History;
use Raven\Core\Extension\History\IdentityGuesser\IdentityGuesser;

class LogTest extends TestCase
{
    public function testLog()
    {
        $gusser = new IdentityGuesser();
        $url = 'http://www.khabaronline.ir/(X(1)S(emohoan2j24nsc4x4w0avlbx))/detail/664224/ict/software';
        $identity = preg_match('/(?<=\/)\d{5,7}(?=\/)/', $url, $matches) ? $matches[0] : null;
        $request = new Request($url, [], '664224');

        dd(History::query()->where('hash', '=', sha1($identity))->count() > 0);
    }
}
