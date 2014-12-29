<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Subscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Config\ConfigManager;
use PHPCR\Shell\Console\Input\StringInput;
use PHPCR\Shell\Event\CommandPreRunEvent;

class AliasSubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Subscriber\AliasSubscriber');
    }

    public function let(
        ConfigManager $config
    ) {
        $this->beConstructedWith(
            $config
        );

        $config->getConfig('alias')->willReturn(array(
            'ls' => 'list:command {arg1}',
            'mv' => 'move {arg1} {arg2}',
        ));
    }

    public function it_should_convert_an_aliased_input_into_a_real_command_input(
        CommandPreRunEvent $event,
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

    public function it_should_ommit_missing_arguments(
        CommandPreRunEvent $event,
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
