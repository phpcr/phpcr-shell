<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\TextHelper');
    }

    function it_should_truncate_text()
    {
        $this->truncate('hello this is some text', 5)->shouldReturn('he...');
    }
}
