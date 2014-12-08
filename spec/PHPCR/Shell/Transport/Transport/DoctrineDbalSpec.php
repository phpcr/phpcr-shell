<?php

namespace spec\PHPCR\Shell\Transport\Transport;

use PhpSpec\ObjectBehavior;

class DoctrineDbalSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Transport\Transport\DoctrineDbal');
    }
}
