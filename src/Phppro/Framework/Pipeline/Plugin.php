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
 * Pipeline Plugin
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Plugin
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
    public function __construct($callable, $method = null)
    {
        if (false === ($callable instanceof \Closure)) {
            $callable = function () use ($callable, $method) {
                $callable->$method();
            };
        }

        $this->setClosure($callable);
    }
    /**
     * @param Context $context
     */
    public function process(Context $context)
    {
        $closure = $this->getClosure();

        $closure($context);
    }
}