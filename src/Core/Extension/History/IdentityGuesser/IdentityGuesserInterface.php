<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Core\Extension\History\IdentityGuesser;

use Raven\Core\Http\Request;

interface IdentityGuesserInterface
{
    /**
     * Guess identity based on request
     *
     * @param Request $request
     * @return string
     */
    public function guess(Request $request);
}