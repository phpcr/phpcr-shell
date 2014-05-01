<?php

namespace spec\PHPCR\Shell\Console\Wizard;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPCR\Shell\Transport\TransportFactoryInterface;
use PHPCR\Shell\Transport\Transport\TransportInterface;

class ConnectionWizardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Wizard\ConnectionWizard');
    }

    function let(
        DialogHelper $dialogHelper,
        TransportFactoryInterface $transportFactory
    )
    {
        $this->beConstructedWith(
            $dialogHelper,
            $transportFactory
        );
    }

    function it_should_produce_a_transport_config(
        InputInterface $input,
        OutputInterface $output,
        DialogHelper $dialogHelper,
        TransportFactoryInterface $transportFactory,
        TransportInterface $transport
    )
    {
        $transportFactory->getTransportNames()->willReturn(array(
            'test transport 1',
            'test transport 2',
        ));

        $output->writeln('(0) test transport 1');
        $output->writeln('(1) test transport 2');
        
        $dialogHelper->ask($output, 'Choose a transport')->willReturn('0');

        $transportFactory->getTransport('test transport 1')->willReturn($transport);
        
        $transport->getTemplateConnectionParameters()->willReturn(array(
            'testhost' => 'http://example.com',
            'foobar' => '',
        ));

        $dialogHelper->ask($output, 'testhost', 'http://example.com')->willReturn('http://barfoo.com');
        $dialogHelper->ask($output, 'foobar', '')->willReturn('bar');

        $this->run($input, $output)->shouldReturn(array(
            'testhost' => 'http://barfoo.com',
            'foobar' => 'bar',
        ));
    }
}
