<?php

namespace Phppro\Framework\Pipeline;


class StepTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithNoPluginsDoNothing()
    {
        $s = new Step();

        $context = new Context();

        $this->assertEquals($context, $s->process($context));
    }
    public function testProcessWithOnePluginExecuteThisPlugin()
    {
        $s = new Step();

        $plugin = $this->getMock('stdClass', array('process'));
        $plugin->expects($this->once())->method('process');

        $s->addPlugin($plugin);

        $s->process(new Context());
    }
    public function testProcessWithMultiplePluginsExecuteThesePlugins()
    {
        $s = new Step();

        $plugin1 = $this->getMock('stdClass', array('process'));
        $plugin1->expects($this->once())->method('process');

        $plugin2 = $this->getMock('stdClass', array('process'));
        $plugin2->expects($this->once())->method('process');

        $s->addPlugin($plugin1);
        $s->addPlugin($plugin2);

        $s->process(new Context());
    }
    public function testProcessWithStepHavingSpecificProcessMethodCallsThisSpecificProcessMethodOnPlugins()
    {
        $s = new Step(null, 'process2');

        $plugin = $this->getMock('stdClass', array('process', 'process2'));
        $plugin->expects($this->once())->method('process2');
        $plugin->expects($this->never())->method('process');

        $s->addPlugin($plugin);

        $s->process(new Context());
    }
    public function testProcessWithOneListenerWhichIsNotAListenerInstanceButIsACallableExecuteThisListener()
    {
        $s = new Step();

        $listener = $this->getMock('stdClass', array('on'));
        $listener->expects($this->once())->method('on');

        $s->addListener(Step::EVENT_ENTER, $listener);

        $s->process(new Context());
    }
    public function testCreateFromJsonForVariousCasesReturnLoadedStep()
    {
        $step = Step::createFromJson(<<<EOJ
            {
                "name": "my-step",
                "method": "theDefaultMethod"
            }
EOJ
        );

        $this->assertEquals('my-step', $step->getName());
        $this->assertEquals('theDefaultMethod', $step->getDefaultMethod());
    }
    public function testAfterWithoutValidCallableAddsAListenerThatIsExecutedRightAfterTheStepHasFinished()
    {
        $s = new Step();

        $_ = new \stdClass;
        $_->executed = array();

        $s->addPlugin(function () use ($_) {
            $_->executed[] = 'plugin';
        });

        $s->after(function () use ($_) {
            $_->executed[] = 'after-listener';
        });

        $s->process(new Context());

        $this->assertEquals(array('plugin', 'after-listener'), $_->executed);
    }
    public function testBeforeWithoutValidCallableAddsAListenerThatIsExecutedRightBeforeTheStepHasFinished()
    {
        $s = new Step();

        $_ = new \stdClass;
        $_->executed = array();

        $s->addPlugin(function () use ($_) {
            $_->executed[] = 'plugin';
        });

        $s->before(function () use ($_) {
            $_->executed[] = 'before-listener';
        });

        $s->process(new Context());

        $this->assertEquals(array('before-listener', 'plugin'), $_->executed);
    }
}
