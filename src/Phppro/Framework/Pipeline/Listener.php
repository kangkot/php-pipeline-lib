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
 * Pipeline Listener
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Listener
{
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
     * @param callable $callable
     * @param string   $method
     */
    public function __construct($callable, $method = 'on')
    {
        if (false === ($callable instanceof \Closure)) {
            if (true === is_callable($callable)) {
                $callable = function (Event $event, Context $context) use ($callable, $method) {
                    call_user_func($callable, $event, $context);
                };
            } elseif (true === is_object($callable) && true === method_exists($callable, $method)) {
                $callable = function (Event $event, Context $context) use ($callable, $method) {
                    call_user_func(array($callable, $method), $event, $context);
                };
            } else {
                throw new \RuntimeException("Listener is not a callable", 10);
            }
        }

        $this->setClosure($callable);
    }
    /**
     * @param Event   $event
     * @param Context $context
     */
    public function on(Event $event, Context $context)
    {
        $closure = $this->getClosure();

        $closure($event, $context);
    }
}