<?php

namespace PHPCR\Shell\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Output\OutputInterface;

class CommandExceptionEvent extends Event
{
    protected $exception;
    protected $output;

    public function __construct(\Exception $exception, OutputInterface $output)
    {
        $this->exception = $exception;
        $this->output = $output;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
