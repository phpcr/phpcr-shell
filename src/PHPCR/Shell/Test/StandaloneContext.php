<?php

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
