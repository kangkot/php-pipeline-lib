<?php

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Condition\AtLeastOne;
use Phppro\Framework\Pipeline\Condition;
use Phppro\Framework\Pipeline\Context;

class AtLeastOneTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithNoConditionsInConstructSetsListOfSubConditionsToEmpty()
    {
        $c = new AtLeastOne();

        $this->assertEquals(array(), $c->getSubConditions());


        $c = new AtLeastOne(null);

        $this->assertEquals(array(), $c->getSubConditions());
    }
    public function testConstructWithSubConditionsAddsTheseSubConditions()
    {
        $subConditions = array(
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
        );

        $c = new AtLeastOne($subConditions);

        $this->assertEquals($subConditions, $c->getSubConditions());
    }
    public function testIsProcessableForVariousConditionCallableReturnGoodResult()
    {
        $c = new AtLeastOne();

        $c->addCondition(function () {
            return true;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertTrue($c->isValid(new Context));

        $c = new AtLeastOne();

        $c->addCondition(function () {
            return false;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertFalse($c->isValid(new Context));
    }
}
