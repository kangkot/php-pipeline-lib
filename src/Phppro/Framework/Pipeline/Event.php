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
 * Pipeline Event
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class Event
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var array
     */
    protected $data;
    /**
     * @param string $type
     * @param array  $args
     */
    public function __construct($type, $args = array())
    {
        $this->setType($type);
        $this->setData($args);
    }
    /**
     * @param array $data
     *
     * @return $this
     */
    protected function setData($data)
    {
        $this->data = $data;

        return $this;
    }
    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return array|null
     */
    public function getData($key = null, $defaultValue = null)
    {
        if (null === $key) {
            return $this->data;
        }

        if (false === isset($this->data[$key])) {
            return $defaultValue;
        }

        return $this->data[$key];
    }
    /**
     * @param string $type
     */
    protected function setType($type)
    {
        $this->type = $type;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}