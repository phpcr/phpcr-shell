<?php

namespace spec\PHPCR\Shell\Transport\Transport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JackrabbitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Transport\Transport\Jackrabbit');
    }
}
