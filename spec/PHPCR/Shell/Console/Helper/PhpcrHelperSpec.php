<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\Transport\TransportRegistryInterface;

class PhpcrHelperSpec extends ObjectBehavior
{
    public function let(
        Profile $profile,
        TransportRegistryInterface $transportRegistry
    ) {
        $this->beConstructedWith($transportRegistry, $profile);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\PhpcrHelper');
    }
}
