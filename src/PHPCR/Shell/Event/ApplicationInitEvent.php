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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Application;

/**
 * This event is fired when the main shell application
 * is initialized.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ApplicationInitEvent extends Event
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }
}
