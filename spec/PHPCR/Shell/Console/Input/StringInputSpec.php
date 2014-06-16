<?php

namespace spec\PHPCR\Shell\Console\Input;

use PhpSpec\ObjectBehavior;

class StringInputSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('foobar');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Input\StringInput');
    }
}
