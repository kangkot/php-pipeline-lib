<?php

namespace Phppro\Framework\Pipeline;


class ListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithNotACallableThrowException()
    {
        $this->setExpectedException('RuntimeException', "Listener is not a callable", 10);

        $this->assertFalse(function_exists('thisIsNotAFunction'));

        new Listener('thisIsNotAFunction');
    }
}
