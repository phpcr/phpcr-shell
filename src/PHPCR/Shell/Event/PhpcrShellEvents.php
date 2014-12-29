<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Event;

/**
 * Events for Phpcr Shell
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrShellEvents
{
    /**
     * Fired when an exception is thrown
     */
    const COMMAND_EXCEPTION = 'command.exception';

    /**
     * Fired before a command is executed
     */
    const COMMAND_PRE_RUN = 'command.pre_run';

    /**
     * Fired when the application is initialized
     */
    const APPLICATION_INIT = 'application.init';

    /**
     * Fired when the profile needs to be populated
     */
    const PROFILE_INIT = 'profile.init';
}
