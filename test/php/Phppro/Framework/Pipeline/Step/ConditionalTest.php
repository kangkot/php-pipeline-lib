<?php

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Context;
use Phppro\Framework\Pipeline\Condition\ContextKeyExist;

class ConditionalTest extends \PHPUnit_Framework_TestCase
{
    public function testIsProcessableForConditionOnKeyExistReturnGoodValue()
    {
        $s = new Conditional();

        $s->addCondition(new ContextKeyExist('theKey'));

        $context = new Context();
        $this->assertFalse($s->isProcessable($context));

        $context->theKey = true;

        $this->assertTrue($s->isProcessable($context));
    }
    public function testProcessForProcessableStepExecuteTheStep()
    {
        $_ = new \stdClass;

        $s = new Conditional();

        $s->addPlugin(function () use ($_) {
            $_->executed = true;
        });

        $s->addCondition(function () {
            return true;
        });

        $s->process(new Context());
        $this->assertTrue(isset($_->executed));
        $this->assertTrue($_->executed);
    }
    public function testProcessForNotProcessableStepDoNotExecuteTheStep()
    {
        $_ = new \stdClass;

        $s = new Conditional();

        $s->addPlugin(function () use ($_) {
            $_->executed = true;
        });

        $s->addCondition(function () {
            return false;
        });

        $s->process(new Context());
        $this->assertFalse(isset($_->executed));
    }
}
