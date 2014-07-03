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
 * Pipeline executable key not exist Condition
 *
 * @author Olivier Hoareau <olivier@phppro.fr>
 */
class ExecutableExist extends Condition
{
    /**
     * @var string
     */
    protected $command;
    /**
     * @param string $command
     */
    public function __construct($command)
    {
        parent::__construct();

        $this->setCommand($command);
    }
    /**
     * @param string $command
     *
     * @return $this
     */
    protected function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }
    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }
    /**
     * @param Context $context
     *
     * @return bool
     */
    public function isValid(Context $context)
    {
        foreach(explode(PATH_SEPARATOR, getenv('PATH')) as $path) {
            if (true === is_file($path . '/' . $this->getCommand())) {
                return true;
            }
        }
        
        return false;
    }
}