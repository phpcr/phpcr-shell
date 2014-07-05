<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;

class TextHelperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\TextHelper');
    }

    public function it_should_truncate_text()
    {
        $this->truncate('hello this is some text', 5)->shouldReturn('he...');
    }
}
