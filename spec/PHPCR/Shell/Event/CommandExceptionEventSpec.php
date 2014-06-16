<?php

namespace spec\PHPCR\Shell\Event;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CommandExceptionEventSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Event\CommandExceptionEvent');
    }

    public function let(
        \Exception $exception,
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->beConstructedWith($exception, $input, $output);
    }

    public function it_should_provide_access_to_event_parameters(
        \Exception $exception,
        OutputInterface $output
    ) {
        $this->getException()->shouldReturn($exception);
        $this->getOutput()->shouldReturn($output);
    }
}
