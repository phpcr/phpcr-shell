<?php

namespace PHPCR\Shell\Console\Helper;

use PHPCR\Shell\Transport\SessionConfig;
use PHPCR\Shell\Config\Profile;

class ProfileHelper
{
    public function getProfileNames()
    {
        return array(
            'profile1',
            'profile2',
        );
    }

    public function getProfile()
    {
        return new Profile('test');
    }

    public function createNewProfile($name)
    {
        $profile = new Profile($name);

        return $profile;
    }
}
