<?php

namespace spec\PHPCR\Shell\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Application;

class ApplicationInitEventSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Event\ApplicationInitEvent');
    }

    function let(
        Application $application
    )
    {
        $this->beConstructedWith($application);
    }

    function it_will_return_the_application(
        Application $application
    )
    {
        $this->getApplication()->shouldReturn($application);
    }
}
