<?php

namespace spec\PHPCR\Shell\Console\Application;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Console\Application\EmbeddedApplication;

class EmbeddedApplicationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(EmbeddedApplication::MODE_COMMAND);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Application\EmbeddedApplication');
    }
}
