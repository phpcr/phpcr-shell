<?php

namespace PHPCR\Shell\Test;

use PHPCR\Shell\Console\Application\EmbeddedApplication;
use PHPCR\Shell\Phpcr\PhpcrSession;

/**
 * Features context.
 */
class EmbeddedContext extends ContextBase
{
    private $application;

    protected function createTester()
    {
        // embbed a new session
        $session = $this->getSession(null, true);

        $this->application = new EmbeddedApplication(EmbeddedApplication::MODE_SHELL);
        $this->application->getHelperSet()->get('phpcr')->setSession(new PhpcrSession($session));

        $tester = new ApplicationTester($this->application);

        return $tester;
    }
}
