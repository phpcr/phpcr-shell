<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use PHPCR\Shell\Console\Helper\TextHelper;
use PHPCR\Shell\Console\Helper\TableHelper;

class ResultFormatterHelperSpec extends ObjectBehavior
{
    public function let(
        TextHelper $textHelper,
        TableHelper $tableHelper
    )
    {
        $this->beConstructedWith($textHelper, $tableHelper);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ResultFormatterHelper');
    }
}
