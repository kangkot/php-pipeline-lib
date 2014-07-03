<?php

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Condition\OnlyOne;
use Phppro\Framework\Pipeline\Condition;
use Phppro\Framework\Pipeline\Context;
use Phppro\Framework\Pipeline\Condition\ContextKeyExist;

class OnlyOneTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithNoConditionsInConstructSetsListOfSubConditionsToEmpty()
    {
        $c = new OnlyOne();

        $this->assertEquals(array(), $c->getSubConditions());


        $c = new OnlyOne(null);

        $this->assertEquals(array(), $c->getSubConditions());
    }
    public function testConstructWithSubConditionsAddsTheseSubConditions()
    {
        $subConditions = array(
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
            new Condition(function () { return true; }),
        );

        $c = new OnlyOne($subConditions);

        $this->assertEquals($subConditions, $c->getSubConditions());
    }
    public function testIsProcessableForVariousConditionCallableReturnGoodResult()
    {
        $c = new OnlyOne();

        $c->addCondition(function () {
            return true;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertTrue($c->isValid(new Context));

        $c = new OnlyOne();

        $c->addCondition(function () {
            return false;
        });
        $c->addCondition(function () {
            return false;
        });

        $this->assertFalse($c->isValid(new Context));


        $c = new OnlyOne();

        $c->addCondition(function () {
            return true;
        });
        $c->addCondition(function () {
            return true;
        });

        $this->assertFalse($c->isValid(new Context));
    }
}
