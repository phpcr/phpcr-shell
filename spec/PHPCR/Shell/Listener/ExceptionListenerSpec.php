<?php

namespace spec\PHPCR\Shell\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Event\CommandExceptionEvent;

class ExceptionListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Listener\ExceptionListener');
    }

    function it_should_output_exception_message_to_console(
        CommandExceptionEvent $event,
        OutputInterface $output
    ) {
        $exception = new \Exception('This is an exception');
        $event->getException()->willReturn($exception);
        $event->getOutput()->willReturn($output);

       $output->writeln('<error>[Exception] This is an exception</error>')->shouldBeCalled();

        $this->handleException($event);
    }
}
