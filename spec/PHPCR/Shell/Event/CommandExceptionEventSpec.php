<?php

namespace spec\PHPCR\Shell\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;

class CommandExceptionEventSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Event\CommandExceptionEvent');
    }

    function let(
        \Exception $exception,
        OutputInterface $output
    ) {
        $this->beConstructedWith($exception, $output);
    }

    function it_should_provide_access_to_event_parameters(
        \Exception $exception,
        OutputInterface $output
    ) {
        $this->getException()->shouldReturn($exception);
        $this->getOutput()->shouldReturn($output);
    }
}
