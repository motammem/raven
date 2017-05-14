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
 * Class Category.
 *
 * @property string $name Name of the category
 * @property Category $parent Parent category
 * @property Category[]|Collection $children Children of category
 */
class Category extends Model
{
    protected $table = 'category';

    /**
     * Parent relationship.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * Children relationship.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
