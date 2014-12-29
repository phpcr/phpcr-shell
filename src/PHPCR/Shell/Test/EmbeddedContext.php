<?php

namespace PHPCR\Shell\Test;

use PHPCR\Shell\Phpcr\PhpcrSession;
use PHPCR\Shell\PhpcrShell;
use PHPCR\Shell\DependencyInjection\Container;

/**
 * Features context
 *
 * Start the shell in the embedded context
 */
class EmbeddedContext extends ContextBase
{
    protected function createTester()
    {
        // embbed a new session
        $session = $this->getSession(null, true);
        $container = new Container(PhpcrShell::MODE_EMBEDDED_SHELL);
        $container->get('phpcr.session_manager')->setSession(new PhpcrSession($session));
        $application = $container->get('application');
        $application->setShowUnsupported(true);
        $tester = new ApplicationTester($application);

        return $tester;
    }
}
