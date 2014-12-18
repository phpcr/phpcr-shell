<?php

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
