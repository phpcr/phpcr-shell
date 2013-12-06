<?php

namespace PHPCR\Shell\Application;

use PHPCR\Shell\Console\Application\SessionApplication;

class SessionApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->transport = $this->getMock(
            'PHPCR\Shell\Console\TransportInterface'
        );
        $this->application = new SessionApplication();
    }

    public function testShellApplication()
    {
    }
}
