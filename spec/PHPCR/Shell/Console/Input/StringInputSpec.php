<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
