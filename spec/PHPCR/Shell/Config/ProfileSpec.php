<?php

namespace spec\PHPCR\Shell\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Transport\TransportConfig;

class ProfileSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'foobar'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Config\Profile');
    }

    function it_has_a_method_to_set_the_transport_config(
        TransportConfig $transportConfig
    )
    {
        $this->setTransportConfig($transportConfig);
    }
}
