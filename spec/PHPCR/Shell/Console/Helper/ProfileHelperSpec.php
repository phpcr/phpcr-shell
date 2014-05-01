<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PHPCR\Shell\Config\Profile;

class ProfileHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ProfileHelper');
    }

    function it_should_return_a_list_of_available_profiles()
    {
        $this->getProfileNames()->shouldReturn(array(
            'profile1',
            'profile2',
        ));
    }

    function it_should_a_profile_object_for_a_valid_profile_name()
    {
        $this->getProfile('profile1');
    }

    function it_should_provide_a_method_to_create_a_new_named_profile()
    {
        $this->createNewProfile('foobar');
    }
}
