<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Event;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Application\ShellApplication;

class CommandExceptionEventSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Event\CommandExceptionEvent');
    }

    public function let(
        \Exception $exception,
        ShellApplication $application,
        OutputInterface $output
    ) {
        $this->beConstructedWith($exception, $application, $output);
    }

    public function it_should_provide_access_to_event_parameters(
        \Exception $exception,
        OutputInterface $output
    ) {
        $this->getException()->shouldReturn($exception);
        $this->getOutput()->shouldReturn($output);
    }
}
