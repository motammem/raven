<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Extension\History;

use Raven\Core\Infrastructure\Model;

class History extends Model
{
    protected $table = 'history';
    public $timestamps = false;
    protected $fillable = [
        'hash',
        'url',
        'visited_at',
    ];

    /**
     * Checks if node exist in history
     *
     * @param $identity string Identity of the node
     * @return bool
     */
    public static function hasNode($identity)
    {
        return self::query()
                ->where('hash', '=', sha1($identity))
                ->count() > 0;
    }
}
