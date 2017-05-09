<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Extension\History\IdentityGuesser;

use Raven\Core\Http\Request;

class IdentityGuesser implements IdentityGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guess(Request $request)
    {
        $identity = $request->getUri();
        if ($request->hasIdentity()) {
            $identity = $request->getIdentity();
        }

        return $identity;
    }
}
