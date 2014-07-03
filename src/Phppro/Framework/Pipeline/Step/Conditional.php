<?php

/**
 * This file is part of the PHPPRO Framework.
 *
 * (c) PHPPRO <oss@phppro.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Step;
use Phppro\Framework\Pipeline\Context;
use Phppro\Framework\Pipeline\Condition;

/**
 * Pipeline Conditional Step
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Conditional extends Step
{
    /**
     * @var Condition[]
     */
    protected $conditions;
    /**
     * @param string $name
     * @param string $defaultMethod
     */
    public function __construct($name = null, $defaultMethod = 'process')
    {
        parent::__construct($name, $defaultMethod);

        $this->conditions = array();
    }
    /**
     * @param mixed $condition
     *
     * @return $this
     */
    public function addCondition($condition)
    {
        if (false === ($condition instanceof Condition)) {
            $condition = new Condition($condition);
        }

        $this->conditions[] = $condition;

        return $this;
    }
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function isProcessable(Context $context)
    {
        foreach($this->conditions as $condition) {
            if (true !== $condition->isValid($context)) {
                return false;
            }
        }

        return true;
    }
    /**
     * @param Context $context
     *
     * @return Context
     */
    public function process(Context $context)
    {
        if (true !== $this->isProcessable($context)) {
            return $context;
        }

        return parent::process($context);
    }
}