<?php

namespace spec\PHPCR\Shell\Transport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransportConfigSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Transport\TransportConfig');
    }

    function it_should_provide_array_access()
    {
        $this['foo'] = 'bar';
        $this->offsetGet('foo')->shouldReturn('bar');
    }

    function it_should_provide_get_and_set()
    {
        $this['foo'] = 'bar';
        $this->get('foo')->shouldReturn('bar');
    }
}
