<?php

namespace Phppro\Framework\Pipeline;


class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithClosureAsCallableExecuteTheClosure()
    {
        $_ = new \stdClass;

        $this->assertFalse(isset($_->executed));

        $p = new Plugin(function () use ($_) {
            $_->executed = true;
        });

        $p->process(new Context());

        $this->assertTrue(isset($_->executed));
        $this->assertTrue($_->executed);
    }
}