<?php

namespace spec\PHPCR\Shell\Transport\Transport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DoctrineDbalSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Transport\Transport\DoctrineDbal');
    }
}
