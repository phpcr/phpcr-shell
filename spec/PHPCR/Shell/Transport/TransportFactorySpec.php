<?php

namespace spec\PHPCR\Shell\Transport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Transport\TransportInterface;

class TransportFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Transport\TransportFactory');
    }

    function it_can_register_transports(
        TransportInterface $transport
    )
    {
        $transport->getName()->willReturn('foobar');
        $this->register($transport);
    }

    function it_can_return_the_names_of_the_transports(
        TransportInterface $transport1,
        TransportInterface $transport2
    )
    {
        $transport1->getName()->willReturn('transport1');
        $transport2->getName()->willReturn('transport2');
        $this->register($transport1);
        $this->register($transport2);

        $this->getTransportNames()->shouldReturn(array(
            'transport1', 'transport2'
        ));
    }

    function it_can_return_a_named_transport_object(
        TransportInterface $transport
    )
    {
        $transport->getName()->willReturn('test');
        $this->register($transport);

        $this->getTransport('test')->shouldReturn($transport);
    }
}
