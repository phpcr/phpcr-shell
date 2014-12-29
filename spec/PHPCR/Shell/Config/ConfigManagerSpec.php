<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class ConfigManagerSpec extends ObjectBehavior
{
    public function let(
        Filesystem $filesystem
    )
    {
        $this->beConstructedWith($filesystem);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Config\ConfigManager');
    }

    public function it_should_have_a_method_to_get_the_users_config_directory()
    {
        putenv('PHPCRSH_HOME=/home/foobar');
        $this->getConfigDir()->shouldReturn('/home/foobar');
    }

    public function it_should_be_able_to_parse_a_config_file_and_return_the_config_as_an_array(
        Filesystem $filesystem
    )
    {
        $dir = __DIR__ . '/fixtures/config';
        putenv('PHPCRSH_HOME=' . $dir);
        $filesystem->exists(Argument::any())->willReturn(true);

        $this->getConfig('alias')->offsetGet('foobar')->shouldReturn('barfoo');
        $this->getConfig('alias')->offsetGet('barfoo')->shouldReturn('foobar');
    }
}
