<?php

namespace PHPCR\Shell\Config;

use PHPCR\Shell\Transport\TransportConfig;

/**
 * Configuration profile object
 */
class Profile
{
    protected $profile = array();
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setTransportConfig($transportConfig)
    {
        $this->profile['transport'] = $transportConfig;
    }

    public function getName()
    {
        return $this->name;
    }
}
