Pipeline Library
================

This library provide support for handling with multi-steps workflow.
It is basically a list of steps containing substeps (plugins) to execute in that specific order.
The library is generic and basic, it is your concern to provide the implementation of each step and plugins.

    $pipeline = new Pipeline();


    $step1 = new Step();

    $step1->addPlugin(function () { echo "First plugin of first step executed."; });
    $step1->addPlugin(function () { echo "Second plugin of first step executed."; });
    $step1->addPlugin(function () { echo "Third plugin of first step executed."; });
    ...

    $pipeline->addStep($step1);


    $step2 = new Step();

    $step2->addPlugin(function () { echo "First plugin of second step executed."; });
    $step2->addPlugin(function () { echo "Second plugin of second step executed."; });
    ...

    $pipeline->addStep($step2);
    ...

    $pipeline->process();

Additionnally, you can share a global context readable/updatable from each step/plugin :

    ...
    $step1->addPlugin(function (Context $context) { $context->variable1 = 12; });
    ...

    $context = $pipeline->process();

    echo $context->variable1;
    // should display "12"

You can also monitor the execution of each step/plugin :

    ...

    // get fired for every event inside the pipeline

    $pipeline->addListener(Step::EVENT_ENTER, function (Step $step, Context $context) {
        echo "Entering in step '{$step->getName()}' ...\n";
    });

    $pipeline->addListener(Step::EVENT_EXIT, function (Step $step, Context $context) {
        echo "Exiting from step '{$step->getName()}'.\n";
    });

    // or only for a specific step ...

    ...
    $step->addListener(Step::EVENT_ENTER, function () { ... });
    ...

You can also use conditional steps :

    ...

    $step = new Step\Conditional();

    $step->addCondition(function (Context $context) {
        return 0 === (date('i', time()) % 2);
    });
    $step->addCondition(new Condition\ContextKeyExist('theKey'));
    ...

    $pipeline->addStep($step);

    // step will be executed only if current time minute is multiple of 2 and if $context->theKey exists ...
    $pipeline->process($context);

    // "OR" conditions ("at least one") :

    $step->addCondition(Condition\AtLeastOne::create()
        ->addCondition(...)
        ->addCondition(...)
        ...
    );

    // "XOR" conditions ("only one") :

    $step->addCondition(Condition\OnlyOne::create()
        ->addCondition(...)
        ->addCondition(...)
        ...
    );
