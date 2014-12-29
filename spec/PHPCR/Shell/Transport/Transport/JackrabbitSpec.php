<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Transport\Transport;

use PhpSpec\ObjectBehavior;

class JackrabbitSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Transport\Transport\Jackrabbit');
    }
}
