<?php

namespace PHPCR\Shell\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CommandExceptionEvent extends Event
{
    protected $exception;
    protected $output;
    protected $input;

    public function __construct(\Exception $exception, InputInterface $input, OutputInterface $output)
    {
        $this->exception = $exception;
        $this->output = $output;
        $this->input = $input;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getInput()
    {
        return $this->input;
    }
}
