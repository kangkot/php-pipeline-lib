<?php

namespace Phppro\Framework\Pipeline;


class PipelineTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithNoStepsDoNothing()
    {
        $p = new Pipeline();

        $context = new Context();

        $this->assertEquals($context, $p->process($context));
    }
    public function testProcessWithOneStepExecuteThisStep()
    {
        $p = new Pipeline();

        $step = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'), array('my-step'));
        $step->expects($this->once())->method('process');

        /** @var Step $step */
        $p->addStep($step);

        $p->process();
    }
    public function testProcessWithMultipleStepsExecuteTheseSteps()
    {
        $p = new Pipeline();

        $step1 = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'), array('step1'));
        $step1->expects($this->once())->method('process');

        $step2 = $this->getMock('Phppro\Framework\Pipeline\\Step', array('process'), array('step2'));
        $step2->expects($this->once())->method('process');

        /** @var Step $step1 */
        $p->addStep($step1);
        /** @var Step $step2 */
        $p->addStep($step2);

        $p->process();
    }
    public function testProcessInAnyCasesReturnSharedContext()
    {
        $p = new Pipeline();

        $step1 = new Step('step1');
        $step1->addPlugin(function (Context $context) {
            $context->myvariable = 13;
        });

        $p->addStep($step1);

        $context = $p->process();

        $this->assertEquals(13, $context->myvariable);
    }
    public function testProcessWithRegisteredStepEnterEventListenerTriggerListenerWhenEnteringSteps()
    {
        $_ = new \stdClass;
        $_->executedSteps = array();

        $this->assertFalse(isset($_->executed));

        $p = new Pipeline();

        $p->addStep(new Step('step1'));
        $p->addStep(new Step('step2'));

        $p->addListener(Step::EVENT_ENTER, function (Event $event, Context $context) use ($_) {
            /** @var Step $step */
            $step = $event->getData('step');
            $_->executedSteps[] = $step->getName();
        });

        $context = $p->process();

        $this->assertEquals(array('step1', 'step2'), $_->executedSteps);
    }
    public function testProcessWithListenerIsACallableMethodCallsThisMethod()
    {
        $p = new Pipeline();

        $listener = $this->getMock('stdClass', array('on'));
        $listener->expects($this->once())->method('on');

        $p->addListener(Pipeline::EVENT_START, array($listener, 'on'));

        $p->process(new Context());
    }
    public function testCreateFromJsonForVariousCasesReturnLoadedPipeline()
    {
        $pipeline = Pipeline::createFromJson(<<<EOJ
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
                    },
                    "c": {
                    }
                 }
            }
EOJ
        );

        $steps = $pipeline->getSteps();

        $this->assertEquals(3, count($steps));

        $this->assertEquals('a', $steps[0]->getName());
        $this->assertEquals(1, count($steps[0]->getSteps()));
        $this->assertEquals('b', $steps[1]->getName());
        $this->assertEquals(2, count($steps[1]->getSteps()));
        $this->assertEquals('c', $steps[2]->getName());
    }
    public function testCreateFromArrayForStepsDoesNotExistReturnPipelineWithNoSteps()
    {
        $pipeline = Pipeline::createFromJson(<<<EOJ
            {
            }
EOJ
        );

        $steps = $pipeline->getSteps();

        $this->assertEquals(0, count($steps));
    }
    public function testGetStepForExistingStepReturnsStep()
    {
        $p = new Pipeline();

        $step1 = new Step('step1');
        $step1->check = 1;
        $step2 = new Step('step2');
        $step2->check = 2;

        $p->addStep($step1);
        $p->addStep($step2);

        $this->assertEquals($step1, $p->getStep('step1'));
        $this->assertEquals($step2, $p->getStep('step2'));
    }
    public function testGetStepForUnknownStepThrowException()
    {
        $p = new Pipeline();

        $p->addStep(new Step('step1'));

        $this->setExpectedException('RuntimeException', "Step 'stepX' does not exist", 500);

        $p->getStep('stepX');
    }
}
