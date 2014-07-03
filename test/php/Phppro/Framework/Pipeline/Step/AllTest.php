<?php

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Condition\All;
use Phppro\Framework\Pipeline\Condition;
use Phppro\Framework\Pipeline\Context;

class AllTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithNoConditionsInConstructSetsListOfSubConditionsToEmpty()
    {
        $c = new All();

        $this->assertEquals(array(), $c->getSubConditions());


        $c = new All(null);

        $this->assertEquals(array(), $c->getSubConditions());
    }
    public function testConstructWithSubConditionsAddsTheseSubConditions()
    {
        $subConditions = array(
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
        );

        $c = new All($subConditions);

        $this->assertEquals($subConditions, $c->getSubConditions());
    }
    public function testIsProcessableForVariousConditionCallableReturnGoodResult()
    {
        $c = new All();

        $c->addCondition(function () {
            return true;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertFalse($c->isValid(new Context));


        $c = new All();

        $c->addCondition(function () {
            return true;
        });
        $c->addCondition(function () {
            return true;
        });

        $this->assertTrue($c->isValid(new Context));
    }
}
