<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
