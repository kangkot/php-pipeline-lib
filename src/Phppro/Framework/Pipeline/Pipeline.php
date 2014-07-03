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
use Phppro\Framework\Trace\TraceAwareInterface;
use Phppro\Framework\Trace\TraceInterface;

/**
 * Pipeline
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Pipeline extends AbstractService implements PipelineInterface, TraceAwareInterface
{
    const EVENT_START = 'events.pipeline.start';
    const EVENT_END   = 'events.pipeline.end';
    /**
     * @var Step[]
     */
    protected $steps;
    /**
     * @var array
     */
    protected $stepIndexByNames;
    /**
     * @var Listener[]
     */
    protected $listeners;
    /**
     * @var TraceInterface
     */
    protected $trace;
    protected $name;
    /**
     * Constructs a new parser
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct(array_merge($options, array('adapterSupported' => false)));

        $this->steps            = array();
        $this->listeners        = array();
        $this->stepIndexByNames = array();
        $this->name             = isset($options['name']) ? $options['name'] : md5(rand(0, 10000) . gethostname() . rand(0, 10000));
    }
    /**
     * Sets the trace
     *
     * @param TraceInterface $trace
     *
     * @return mixed
     */
    public function setTrace(TraceInterface $trace)
    {
        $this->trace = $trace;

        return $this;
    }
    /**
     * Returns the trace
     *
     * @return TraceInterface
     */
    public function getTrace()
    {
        return $this->trace;
    }
    /**
     * Processes the pipeline
     *
     * @param Context $context
     *
     * @return Context
     */
    public function process(Context $context = null)
    {
        if (null === $context) {
            $context = new Context();
        }

        $this->trace('pipeline.process.started', $this->name);

        $this->fireEvent(new Event(Pipeline::EVENT_START, array('pipeline' => $this)), $context);

        foreach($this->steps as $step) {
            $this->trace('pipeline.process.step.started', $this->name, $step->getName());
            foreach($this->listeners as $eventType => $listeners) {
                foreach($listeners as $listener) {
                    $step->addListener($eventType, $listener);
                }
            }
            $step->process($context);
            $this->trace('pipeline.process.step.completed', $this->name, $step->getName());
        }

        $this->fireEvent(new Event(Pipeline::EVENT_END, array('pipeline' => $this)), $context);

        $this->trace('pipeline.process.completed', $this->name);

        return $context;
    }
    /**
     * Returns the steps
     *
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }
    /**
     * Adds the specified step
     *
     * @param Step $step
     *
     * @return $this
     */
    public function addStep(Step $step)
    {
        $this->steps[] = $step;

        if (0 < strlen($step->getName())) {
            $lastKey = array_pop(array_keys($this->steps));

            $this->stepIndexByNames[$step->getName()] = $lastKey;
        }

        return $this;
    }
    /**
     * @param string $name
     *
     * @return Step
     */
    public function getStep($name)
    {
        $key = null;

        if (true === is_numeric($name)) {
            $key = $name;
        } elseif (true === isset($this->stepIndexByNames[$name])) {
            $key = $this->stepIndexByNames[$name];
        } else {
            $this->throwException("Step '%s' does not exist", $name);
        }

        return $this->steps[$key];
    }
    /**
     * Adds the listener
     *
     * @param string $eventType
     * @param mixed  $listener
     *
     * @return $this
     */
    public function addListener($eventType, $listener)
    {
        if (false === isset($this->listeners[$eventType])) {
            $this->listeners[$eventType] = array();
        }

        if (false === ($listener instanceof Listener)) {
            $listener = new Listener($listener);
        }

        $this->listeners[$eventType][] = $listener;

        return $this;
    }
    /**
     * Fires the specified event to listeners
     *
     * @param Event   $event
     * @param Context $context
     *
     * @return $this
     */
    protected function fireEvent(Event $event, Context $context)
    {
        if (false === isset($this->listeners[$event->getType()])) {
            return $this;
        }

        foreach($this->listeners[$event->getType()] as $listener) {
            /** @var Listener $listener */
            $listener->on($event, $context);
        }

        return $this;
    }
    /**
     * Creates a new pipeline from Json description
     *
     * @param string $json
     *
     * @return $this
     */
    public static function createFromJson($json)
    {
        return static::createFromArray(json_decode($json, true));
    }
    /**
     * Creates a new pipeline from array description
     *
     * @param array $def
     *
     * @return $this
     */
    public static function createFromArray($def)
    {
        /** @var Pipeline $pipeline  */
        $pipeline = new static();

        if (false === isset($def['steps']) || false === is_array($def['steps'])) {
            $def['steps'] = array();
        }

        foreach($def['steps'] as $stepName => $stepDef) {
            if (true === isset($stepDef['steps'])) {
                $step = Composite::createFromArray(array_merge(array('name' => $stepName), $stepDef));
            } else {
                $step = Step::createFromArray(array_merge(array('name' => $stepName), $stepDef));
            }

            $pipeline->addStep($step);
        }

        return $pipeline;
    }
}