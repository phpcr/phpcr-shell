<?php

namespace PHPCR\Shell\Console\Input;

use Symfony\Component\Console\Input\StringInput as BaseInput;

class StringInput extends BaseInput
{
    protected $rawCommand;

    public function __construct($command)
    {
        $this->rawCommand = $command;

        parent::__construct($command);
    }

    public function getRawCommand()
    {
        return $this->rawCommand;
    }
}
