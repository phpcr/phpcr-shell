<?php

namespace spec\PHPCR\Shell\Console\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringInputSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foobar');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Input\StringInput');
    }
}
