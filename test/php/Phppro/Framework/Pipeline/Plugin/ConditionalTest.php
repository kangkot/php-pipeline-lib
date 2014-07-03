<?php

namespace Phppro\Framework\Pipeline\Plugin;

use Phppro\Framework\Pipeline\Context;
use Phppro\Framework\Pipeline\Condition\ContextKeyExist;

class ConditionalTest extends \PHPUnit_Framework_TestCase
{
    public function testIsProcessableForConditionOnKeyExistReturnGoodValue()
    {
        $p = new Conditional(function () {});

        $p->addCondition(new ContextKeyExist('theKey'));

        $context = new Context();
        $this->assertFalse($p->isProcessable($context));

        $context->theKey = true;

        $this->assertTrue($p->isProcessable($context));
    }
    public function testProcessForProcessablePluginExecuteThePlugin()
    {
        $_ = new \stdClass;

        $p = new Conditional(function () use ($_) {
            $_->executed = true;
        });

        $p->addCondition(function () {
            return true;
        });

        $p->process(new Context());
        $this->assertTrue(isset($_->executed));
        $this->assertTrue($_->executed);
    }
    public function testProcessForNotProcessableStepDoNotExecuteThePlugin()
    {
        $_ = new \stdClass;

        $p = new Conditional(function () use ($_) {
            $_->executed = true;
        });

        $p->addCondition(function () {
            return false;
        });

        $p->process(new Context());
        $this->assertFalse(isset($_->executed));
    }
}
