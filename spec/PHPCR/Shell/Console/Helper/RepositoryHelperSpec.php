<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\RepositoryInterface;
use PHPCR\Shell\Console\Helper\PhpcrHelper;

class RepositoryHelperSpec extends ObjectBehavior
{
    function let(
        PhpcrHelper $phpcrHelper
    ) {
        $this->beConstructedWith($phpcrHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\RepositoryHelper');
    }

    function it_provides_a_method_to_say_if_a_descriptor_exists_or_not(
        PhpcrHelper $phpcrHelper,
        RepositoryInterface $repository
    ) {
        $phpcrHelper->getRepository()->willReturn($repository);
        $repository->getDescriptorKeys()->willReturn(array(
            'foo', 'bar'
        ));
        $repository->getDescriptor('foo')->willReturn('foo');
        $repository->getDescriptor('bar')->willReturn('foo');

        $this->hasDescriptor('foo')->shouldReturn(true);
    }
}
