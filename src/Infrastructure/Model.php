<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Infrastructure;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public function push()
    {
        if ( ! $this->save()) {
            return false;
        }

        // To sync all of the relationships to the database, we will simply spin through
        // the relationships and save each model via this "push" method, which allows
        // us to recurse into all of these nested relations for the model instance.
        foreach ($this->relations as $relation => $models) {
            $models = $models instanceof Collection ? $models->all() : [$models];

            if ($this->$relation() instanceof MorphMany) {
                $this->$relation()->saveMany($models);
            }

            foreach (array_filter($models) as $model) {
                if ( ! $model->push()) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function insertIgnore($array)
    {
        $a = new static();
        if ($a->timestamps) {
            $now = \Carbon\Carbon::now();
            $array['created_at'] = $now;
            $array['updated_at'] = $now;
        }
        self::query()->insert('INSERT IGNORE INTO '.$a->table.' ('.implode(',', array_keys($array)).
            ') values (?'.str_repeat(',?', count($array) - 1).')', array_values($array));
    }
}
