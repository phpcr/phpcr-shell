<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ConfigHelper');
    }

    function it_should_have_a_method_to_get_the_users_config_directory()
    {
        putenv('PHPCRSH_HOME=/home/foobar');
        $this->getConfigDir()->shouldReturn('/home/foobar');
    }
}
