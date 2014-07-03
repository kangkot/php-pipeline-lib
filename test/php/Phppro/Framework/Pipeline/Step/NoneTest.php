<?php

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Condition\None;
use Phppro\Framework\Pipeline\Condition;
use Phppro\Framework\Pipeline\Context;

class NoneTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithNoConditionsInConstructSetsListOfSubConditionsToEmpty()
    {
        $c = new None();

        $this->assertEquals(array(), $c->getSubConditions());


        $c = new None(null);

        $this->assertEquals(array(), $c->getSubConditions());
    }
    public function testConstructWithSubConditionsAddsTheseSubConditions()
    {
        $subConditions = array(
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
        );

        $c = new None($subConditions);
    }
    public function testIsProcessableForVariousConditionCallableReturnGoodResult()
    {
        $c = new None();

        $c->addCondition(function () {
            return true;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertFalse($c->isValid(new Context));


        $c = new None();

        $c->addCondition(function () {
            return false;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertTrue($c->isValid(new Context));
    }
}
