<?php

namespace Phppro\Framework\Pipeline;


class ConditionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValidForClosureThatReturnTrueReturnsTrue()
    {
        $c = new Condition(function (Context $context) {
            return true;
        });

        $this->assertTrue($c->isValid(new Context()));
    }
    public function testIsValidForClosureThatReturnFalseReturnsFalse()
    {
        $c = new Condition(function (Context $context) {
            return false;
        });

        $this->assertFalse($c->isValid(new Context()));
    }
    public function testIsValidForCallableReturnTheValueOfCallableResult()
    {
        $callable = $this->getMock('stdClass', array('isValid'));
        $callable->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $c = new Condition($callable);

        $this->assertTrue($c->isValid(new Context()));


        $callable = $this->getMock('stdClass', array('isValid'));
        $callable->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $c = new Condition($callable);

        $this->assertFalse($c->isValid(new Context()));


        $callable = $this->getMock('stdClass', array('isValid'));
        $callable->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $c = new Condition(array($callable, 'isValid'));

        $this->assertFalse($c->isValid(new Context()));
    }
    public function testConstructForNotCallableThrowException()
    {
        $this->setExpectedException('RuntimeException', 'Condition is not a callable', 11);
        $this->assertFalse(function_exists('thisIsNotAFunction'));
        new Condition('thisIsNotAFunction');
    }
    public function testCreateInStaticContextReturnsANewInstance()
    {
        $this->assertEquals('Phppro\Framework\Pipeline\\Condition', get_class(Condition::create()));
    }
}
