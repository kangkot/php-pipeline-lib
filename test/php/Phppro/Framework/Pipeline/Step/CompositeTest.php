<?php

namespace Phppro\Framework\Pipeline\Step;

use Phppro\Framework\Pipeline\Context;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithNoSubStepsDoNothing()
    {
        $s = new Composite();

        $context = new Context();

        $this->assertEquals($context, $s->process($context));
    }
    public function testProcessWithOneSubStepExecuteThisSubStep()
    {
        $s = new Composite();

        $subStep = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'));
        $subStep->expects($this->once())->method('process');

        $s->addStep($subStep);

        $s->process(new Context());
    }
    public function testProcessWithMultipleSubStepsExecuteTheseSubSteps()
    {
        $s = new Composite();

        $subStep1 = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'));
        $subStep1->expects($this->once())->method('process');

        $subStep2 = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'));
        $subStep2->expects($this->once())->method('process');

        $s->addStep($subStep1);
        $s->addStep($subStep2);

        $s->process(new Context());
    }
    public function testProcessWithMultipleSubStepsWithAtLeastOneSubStepWhichIsCompositeExecuteAllSubStepsRecursively()
    {
        $s = new Composite();

        $subStep1 = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'));
        $subStep1->expects($this->once())->method('process');

        $subStep2 = new Composite();

        $subStep2a = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'));
        $subStep2a->expects($this->once())->method('process');

        $subStep2b = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'));
        $subStep2b->expects($this->once())->method('process');

        $subStep2->addStep($subStep2a);
        $subStep2->addStep($subStep2b);

        $s->addStep($subStep1);
        $s->addStep($subStep2);

        $s->process(new Context());
    }
    public function testCreateFromJsonForVariousCasesReturnLoadedComposite()
    {
        $composite = Composite::createFromJson(<<<EOJ
            {
                "steps": {
                    "a": {
                        "steps": {
                            "x": {
                            }
                        }
                    },
                    "b": {
                        "steps": {
                            "y": {
                            },
                            "z": {
                            }
                        }
                    }
                 }
            }
EOJ
        );

        $steps = $composite->getSteps();

        $this->assertEquals(2, count($steps));

        $this->assertEquals('a', $steps[0]->getName());
        $this->assertEquals(1, count($steps[0]->getSteps()));
        $this->assertEquals('b', $steps[1]->getName());
        $this->assertEquals(2, count($steps[1]->getSteps()));
    }
    public function testCreateFromArrayForStepsDoesNotExistReturnCompositeWithNoSteps()
    {
        $composite = Composite::createFromJson(<<<EOJ
            {
            }
EOJ
        );

        $steps = $composite->getSteps();

        $this->assertEquals(0, count($steps));
    }
}
