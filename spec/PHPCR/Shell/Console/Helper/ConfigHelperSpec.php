<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class ConfigHelperSpec extends ObjectBehavior
{
    function let(
        Filesystem $filesystem
    )
    {
        $this->beConstructedWith($filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ConfigHelper');
    }

    function it_should_have_a_method_to_get_the_users_config_directory()
    {
        putenv('PHPCRSH_HOME=/home/foobar');
        $this->getConfigDir()->shouldReturn('/home/foobar');
    }

    function it_should_be_able_to_parse_a_config_file_and_return_the_config_as_an_array(
        Filesystem $filesystem
    )
    {
        $dir = __DIR__ . '/fixtures/config';
        putenv('PHPCRSH_HOME=' . $dir);
        $filesystem->exists(Argument::any())->willReturn(true);

        $config = $this->getConfig('alias')->shouldReturn(array(
            'foobar' => 'barfoo',
            'barfoo' => 'foobar',
        ));

    }
}
