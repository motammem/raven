<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Core\Exception;

use Throwable;

class SpiderCloseException extends \Exception
{
    /**
     * @var string
     */
    private $cause;

    /**
     * @var int
     */
    private $context;

    public function __construct($cause, $context = [], $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->cause = $cause;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * @return int
     */
    public function getContext()
    {
        return $this->context;
    }
}
