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
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Application\ShellApplication;

class CommandExceptionEvent extends Event
{
    protected $exception;
    protected $output;
    protected $application;

    public function __construct(\Exception $exception, ShellApplication $application, OutputInterface $output)
    {
        $this->exception = $exception;
        $this->output = $output;
        $this->application = $application;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getApplication()
    {
        return $this->application;
    }
}
