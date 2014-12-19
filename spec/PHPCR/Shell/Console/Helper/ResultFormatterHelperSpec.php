<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use PHPCR\Shell\Console\Helper\TextHelper;
use PHPCR\Shell\Console\Helper\TableHelper;
use PHPCR\Shell\Config\Config;

class ResultFormatterHelperSpec extends ObjectBehavior
{
    public function let(
        TextHelper $textHelper,
        TableHelper $tableHelper,
        Config $config
    )
    {
        $this->beConstructedWith($textHelper, $tableHelper, $config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ResultFormatterHelper');
    }
}
