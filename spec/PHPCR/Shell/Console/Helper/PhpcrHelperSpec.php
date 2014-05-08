<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\Transport\TransportRegistryInterface;

class PhpcrHelperSpec extends ObjectBehavior
{
    function let(
        Profile $profile,
        TransportRegistryInterface $transportRegistry
    ) {
        $this->beConstructedWith($transportRegistry, $profile);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\PhpcrHelper');
    }
}
