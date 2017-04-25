<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core;

trait Identity
{
    public function __construct($identity, $attributes = [])
    {
        $attributes['target_site_id'] = $identity;
        parent::__construct($attributes);
    }
}
