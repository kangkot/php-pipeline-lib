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

use Phppro\Framework\Common\Base\AbstractService;
use Phppro\Framework\Pipeline\Step\Composite;

/**
 * Pipeline
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
interface PipelineInterface
{
    /**
     * Processes the pipeline
     *
     * @param Context $context
     *
     * @return Context
     */
    public function process(Context $context = null);
    /**
     * Returns the steps
     *
     * @return Step[]
     */
    public function getSteps();
    /**
     * Adds the specified step
     *
     * @param Step $step
     *
     * @return $this
     */
    public function addStep(Step $step);
    /**
     * @param string $name
     *
     * @return Step
     */
    public function getStep($name);
    /**
     * Adds the listener
     *
     * @param string $eventType
     * @param mixed  $listener
     *
     * @return $this
     */
    public function addListener($eventType, $listener);
}