<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResultFormatterHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ResultFormatterHelper');
    }
}
