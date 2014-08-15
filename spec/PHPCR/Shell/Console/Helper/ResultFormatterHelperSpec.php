<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Console\Helper\TextHelper;

class ResultFormatterHelperSpec extends ObjectBehavior
{
    function let(
        TextHelper $textHelper
    )
    {
        $this->beConstructedWith($textHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ResultFormatterHelper');
    }
}
