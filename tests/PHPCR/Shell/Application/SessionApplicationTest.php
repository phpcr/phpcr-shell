<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
