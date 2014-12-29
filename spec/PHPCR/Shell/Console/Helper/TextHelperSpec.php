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
