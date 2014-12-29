<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Phpcr;

use PhpSpec\ObjectBehavior;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\Transport\TransportRegistryInterface;

class SessionManagerSpec extends ObjectBehavior
{
    public function let(
        Profile $profile,
        TransportRegistryInterface $transportRegistry
    ) {
        $this->beConstructedWith($transportRegistry, $profile);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Phpcr\SessionManager');
    }
}
