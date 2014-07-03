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
 * Pipeline Context key not exist Condition
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class ContextKeyNotExist extends Condition
{
    /**
     * @var string
     */
    protected $key;
    /**
     * @param string $key
     */
    public function __construct($key)
    {
        parent::__construct();

        $this->setKey($key);
    }
    /**
     * @param string $key
     *
     * @return $this
     */
    protected function setKey($key)
    {
        $this->key = $key;

        return $this;
    }
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function isValid(Context $context)
    {
        return false === property_exists($context, $this->getKey());
    }

}