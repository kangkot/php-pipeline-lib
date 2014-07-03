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
 * Pipeline Step
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Step
{
    const EVENT_ENTER = 'events.step.enter';
    const EVENT_EXIT  = 'events.step.exit';
    /**
     * @var string
     */
    protected $name;
    /**
     * @var Plugin[]
     */
    protected $plugins;
    /**
     * @var Listener[]
     */
    protected $listeners;
    /**
     * @var string
     */
    protected $defaultMethod;
    /**
     * Constructs a new step
     *
     * @param null $name
     * @param string $defaultMethod
     */
    public function __construct($name = null, $defaultMethod = 'process')
    {
        $this->plugins = array();
        $this->setName($name);
        $this->setDefaultMethod($defaultMethod);
    }
    /**
     * @param string $name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $defaultMethod
     *
     * @return $this
     */
    protected function setDefaultMethod($defaultMethod)
    {
        $this->defaultMethod = $defaultMethod;

        return $this;
    }
    /**
     * @return string
     */
    public function getDefaultMethod()
    {
        return $this->defaultMethod;
    }
    /**
     * @param mixed $plugin
     *
     * @return $this
     */
    public function addPlugin($plugin)
    {
        $this->plugins[] = new Plugin($plugin, $this->getDefaultMethod());

        return $this;
    }
    /**
     * @param Context $context
     *
     * @return Context
     */
    public function process(Context $context)
    {
        $this->fireEvent(new Event(Step::EVENT_ENTER, array('step' => $this)), $context);

        foreach($this->plugins as $plugin) {
            $plugin->process($context);
        }

        $this->fireEvent(new Event(Step::EVENT_EXIT, array('step' => $this)), $context);

        return $context;
    }
    /**
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
     * @param string $json
     *
     * @return Step
     */
    public static function createFromJson($json)
    {
        return static::createFromArray(json_decode($json, true));
    }
    /**
     * @param array $def
     *
     * @return Step
     */
    public static function createFromArray($def)
    {
        $step = new Step(true === isset($def['name']) ? $def['name'] : null, true === isset($def['method']) ? $def['method'] : 'process');

        return $step;
    }
    /**
     * @param mixed $listener
     *
     * @return $this
     */
    public function after($listener)
    {
        return $this->addListener(static::EVENT_EXIT, $listener);
    }
    /**
     * @param mixed $listener
     *
     * @return $this
     */
    public function before($listener)
    {
        return $this->addListener(static::EVENT_ENTER, $listener);
    }
    /**
     * @return bool
     */
    public function hasPlugins()
    {
        return 0 < count($this->plugins);
    }
}