<?php

/**
 * This file is part of the PHPPRO Framework.
 *
 * (c) PHPPRO <oss@phppro.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phppro\Framework\Pipeline\Condition;


use Phppro\Framework\Pipeline\Context;
use Phppro\Framework\Pipeline\Condition;

/**
 * Pipeline At least one Condition
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class AtLeastOne extends Condition
{
    /**
     * @var Condition[]
     */
    protected $subConditions;
    /**
     * @param array $subConditions
     */
    public function __construct($subConditions = array())
    {
        parent::__construct();

        $this->subConditions = array();

        if (false === is_array($subConditions)) {
            $subConditions = array();
        }

        foreach($subConditions as $subCondition) {
            $this->addCondition($subCondition);
        }
    }
    /**
     * @return Condition[]
     */
    public function getSubConditions()
    {
        return $this->subConditions;
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

        $this->subConditions[] = $condition;

        return $this;
    }
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function isValid(Context $context)
    {
        foreach($this->subConditions as $subCondition) {
            if (true === $subCondition->isValid($context)) {
                return true;
            }
        }

        return false;
    }
}