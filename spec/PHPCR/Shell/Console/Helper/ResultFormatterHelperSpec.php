<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace spec\PHPCR\Shell\Console\Helper;

use PHPCR\Shell\Config\Config;
use PHPCR\Shell\Console\Helper\TextHelper;
use PhpSpec\ObjectBehavior;

class ResultFormatterHelperSpec extends ObjectBehavior
{
    public function let(
        TextHelper $textHelper,
        Config $config
    ) {
        $this->beConstructedWith($textHelper, $config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ResultFormatterHelper');
    }
}
