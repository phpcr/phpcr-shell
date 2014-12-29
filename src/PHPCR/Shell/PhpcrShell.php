<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell;

use PHPCR\Shell\Console\Application\SessionApplication;
use PHPCR\SessionInterface;
use PHPCR\Shell\DependencyInjection\Container;
use PHPCR\Shell\Console\Application\Shell;
use PHPCR\Shell\Phpcr\PhpcrSession;

/**
 * PHPCRShell entry point
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrShell
{
    const APP_NAME = 'PHPCRSH';
    const APP_VERSION = 'dev-master';

    const MODE_EMBEDDED_SHELL = 'shell';
    const MODE_EMBEDDED_COMMAND = 'command';
    const MODE_STANDALONE = 'standalon';

    /**
     * Create a new embedded shell
     *
     * @param  SessionInterface $session
     * @return Shell
     */
    public static function createEmbeddedShell(SessionInterface $session)
    {
        $container = new Container(self::MODE_EMBEDDED_SHELL);
        $container->get('phpcr.session_manager')->setSession(new PhpcrSession($session));
        $application = $container->get('application');

        return new Shell($application);
    }

    /**
     * Create a new (non-interactive) embedded application (e.g. for running
     * single commands)
     *
     * @param  SessionInterface    $session
     * @return EmbeddedApplication
     */
    public static function createEmbeddedApplication(SessionInterface $session)
    {
        $container = new Container(self::MODE_EMBEDDED_COMMAND);
        $container->get('phpcr.session_manager')->setSession(new PhpcrSession($session));
        $application = $container->get('application');

        return $application;
    }

    /**
     * Create a new standalone shell application
     *
     * @return SessionApplication
     */
    public static function createShell()
    {
        return new SessionApplication();
    }
}
