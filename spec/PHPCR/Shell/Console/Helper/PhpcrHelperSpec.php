<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;

class PhpcrHelperSpec extends ObjectBehavior
{
    function let(
        InputInterface $input
    ) {
        $this->beConstructedWith($input);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\PhpcrHelper');
    }
}
