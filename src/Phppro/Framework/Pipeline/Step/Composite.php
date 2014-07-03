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

/**
 * Pipeline Composite Step
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Composite extends Step
{
    /**
     * @var Step[]
     */
    protected $steps;
    /**
     * @param string $name
     * @param string $defaultMethod
     */
    public function __construct($name = null, $defaultMethod = 'process')
    {
        parent::__construct($name, $defaultMethod);

        $this->steps = array();
    }
    /**
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }
    /**
     * @param Context $context
     *
     * @return Context
     */
    public function process(Context $context)
    {
        foreach($this->steps as $step) {
            $step->process($context);
        }

        return parent::process($context);
    }
    /**
     * @param Step $step
     *
     * @return $this
     */
    public function addStep(Step $step)
    {
        $this->steps[] = $step;

        return $this;
    }
    /**
     * @param string $json
     *
     * @return $this
     */
    public static function createFromJson($json)
    {
        return static::createFromArray(json_decode($json, true));
    }
    /**
     * @param array $def
     *
     * @return $this
     */
    public static function createFromArray($def)
    {
        $composite = new Composite(true === isset($def['name']) ? $def['name'] : null, true === isset($def['method']) ? $def['method'] : 'process');

        if (false === isset($def['steps']) || false === is_array($def['steps'])) {
            $def['steps'] = array();
        }

        foreach($def['steps'] as $stepName => $stepDef) {
            if (true === isset($stepDef['steps'])) {
                $step = Composite::createFromArray(array_merge(array('name' => $stepName), $stepDef));
            } else {
                $step = Step::createFromArray(array_merge(array('name' => $stepName), $stepDef));
            }

            $composite->addStep($step);
        }

        return $composite;
    }
}