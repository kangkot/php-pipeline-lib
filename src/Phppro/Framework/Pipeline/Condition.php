<?php

/**
 * This file is part of the PHPPRO Framework.
 *
 * (c) PHPPRO <oss@phppro.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phppro\Framework\Pipeline;

/**
 * Pipeline Condition
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Condition
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }
    /**
     * @var callable
     */
    protected $closure;
    /**
     * @param callable $closure
     *
     * @return $this
     */
    protected function setClosure($closure)
    {
        $this->closure = $closure;

        return $this;
    }
    /**
     * @return callable
     */
    protected function getClosure()
    {
        return $this->closure;
    }
    /**
     * @param callable|null $callable
     * @param string        $method
     */
    public function __construct($callable = null, $method = 'isValid')
    {
        if (null === $callable) {
            $callable = $this;
        }

        if (false === ($callable instanceof \Closure)) {
            if (true === is_callable($callable)) {
                $callable = function (Context $context) use ($callable, $method) {
                    return call_user_func($callable, $context);
                };
            } elseif (true === is_object($callable) && true === method_exists($this, $method)) {
                $callable = function (Context $context) use ($callable, $method) {
                    return call_user_func(array($callable, $method), $context);
                };
            } else {
                throw new \RuntimeException("Condition is not a callable", 11);
            }
        }

        $this->setClosure($callable);
    }
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function isValid(Context $context)
    {
        $closure = $this->getClosure();

        return $closure($context);
    }
}