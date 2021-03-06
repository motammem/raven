<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Category;

use Raven\Core\Infrastructure\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Source.
 *
 * @property string $name Name of the source
 * @property string $domain Domain of the source
 * @property Collection|Spider[] $spider Spiders of source
 */
class Source extends Model
{
    protected $table = 'source';

    /**
     * Spider relationship.
     */
    public function spiders()
    {
        return $this->hasMany(Spider::class, 'source_id', 'id');
    }
}
