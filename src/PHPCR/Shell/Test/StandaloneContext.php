<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Test;

use PHPCR\Shell\Console\Application\SessionApplication;

/**
 * Features context.
 */
class StandaloneContext extends ContextBase
{
    protected function createTester()
    {
        $sessionApplication = new SessionApplication();
        $shellApplication = $sessionApplication->getShellApplication();
        $tester = new ApplicationTester($sessionApplication, $shellApplication);
        $tester->run(array(
            '--transport' => 'jackrabbit',
            '--no-interaction' => true,
            '--unsupported' => true, // test all the commands, even if they are unsupported (we test for the fail)
        ), array(
            'interactive' => true,
        ));

        return $tester;
    }
}
