<?php

namespace spec\PHPCR\Shell\Console\Application;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ShellApplicationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Application\ShellApplication');
    }
}
