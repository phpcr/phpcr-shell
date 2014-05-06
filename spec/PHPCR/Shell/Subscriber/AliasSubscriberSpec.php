<?php

namespace spec\PHPCR\Shell\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Console\Helper\ConfigHelper;
use PHPCR\Shell\Console\Input\StringInput;
use PHPCR\Shell\Event\CommandPreRunEvent;
use Symfony\Component\Console\Helper\HelperSet;

class AliasSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Subscriber\AliasSubscriber');
    }

    function let(
        HelperSet $helperSet,
        ConfigHelper $config
    ) {
        $helperSet->get('config')->willReturn($config);

        $this->beConstructedWith(
            $helperSet
        );

        $config->getConfig('alias')->willReturn(array(
            'ls' => 'list:command {arg1}',
            'mv' => 'move {arg1} {arg2}',
        ));
    }

    function it_should_convert_an_aliased_input_into_a_real_command_input(
        CommandPreRunEvent $event,
        ConfigHelper $config,
        StringInput $input
    ) {
        $event->getInput()->willReturn($input);
        $input->getFirstArgument()->willReturn('ls');
        $input->getTokens()->willReturn(array(
            'ls', 'me'
        ));
        $event->setInput(Argument::type('PHPCR\Shell\Console\Input\StringInput'))->shouldBeCalled();

        $this->handleAlias($event)->shouldReturn('list:command me');
    }

    function it_should_ommit_missing_arguments(
        CommandPreRunEvent $event,
        ConfigHelper $config,
        StringInput $input
    ) {
        $event->getInput()->willReturn($input);
        $input->getFirstArgument()->willReturn('ls');
        $input->getTokens()->willReturn(array(
            'ls'
        ));
        $event->setInput(Argument::type('PHPCR\Shell\Console\Input\StringInput'))->shouldBeCalled();

        $this->handleAlias($event)->shouldReturn('list:command');
    }
}
