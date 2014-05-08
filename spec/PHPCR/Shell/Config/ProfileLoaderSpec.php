<?php

namespace spec\PHPCR\Shell\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Console\Helper\ConfigHelper;
use PHPCR\Shell\Config\Profile;
use Symfony\Component\Filesystem\Filesystem;

class ProfileLoaderSpec extends ObjectBehavior
{
    function let(
        ConfigHelper $configHelper,
        Filesystem $filesystem
    )
    {
        $configHelper->getConfigDir()->willReturn(__DIR__);
        $this->beConstructedWith($configHelper, $filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Config\ProfileLoader');
    }

    function it_should_list_profile_names()
    {
        $this->getProfileNames()->shouldReturn(array(
            'one', 'two'
        ));
    }

    function it_should_load_data_into_a_given_profile(
        Profile $profile,
        Filesystem $filesystem
    )
    {
        $profile->getName()->willReturn('one');
        $profile->set('transport', array(
            'name' => 'foobar',
            'bar_foo' => 'barfoo',
            'foo_bar' => 'foobar',
        ))->shouldBeCalled();
        $profile->set('phpcr', array(
            'username' => 'username',
            'password' => 'password',
            'workspace' => 'default',
        ))->shouldBeCalled();

        $this->loadProfile($profile);
    }

    function it_should_save_a_given_profile(
        Profile $profile,
        Filesystem $filesystem
    )
    {
        $profile->getName()->willReturn('newprofile');
        $profile->toArray()->willReturn(array(
            'transport' => array(
                'name' => 'test_transport',
                'option1' => 'value1',
            ),
            'phpcr' => array(
                'username' => 'daniel',
                'password' => 'leech',
            ),
        ));
        $filesystem->dumpFile(Argument::type('string'), <<<EOT
transport:
    name: test_transport
    option1: value1
phpcr:
    username: daniel
    password: leech

EOT
        , 0600)->shouldBeCalled();
        $this->saveProfile($profile);
    }
}
