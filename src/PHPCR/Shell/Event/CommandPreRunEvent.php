<?php

namespace PHPCR\Shell\Event;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;

class CommandPreRunEvent extends Event
{
    protected $commandName;
    protected $input;

    public function __construct($commandName, InputInterface $input)
    {
        $this->commandName = $commandName;
        $this->input = $input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setInput($input)
    {
        $this->input = $input;
    }

    public function getCommandName()
    {
        return $this->commandName;
    }

}
