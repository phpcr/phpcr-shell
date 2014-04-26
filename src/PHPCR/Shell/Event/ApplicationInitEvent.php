<?php

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
