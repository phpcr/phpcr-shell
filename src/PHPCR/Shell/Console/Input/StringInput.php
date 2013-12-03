<?php

namespace PHPCR\Shell\Console\Input;

use Symfony\Component\Console\Input\StringInput as BaseInput;

class StringInput extends BaseInput
{
    protected $rawCommand;

    public function __construct($command)
    {
        $this->rawCommand = trim($command);

        if (strpos(strtolower($this->rawCommand), 'select') === 0) {
            $command = 'select' . substr($command, 6);
        }

        parent::__construct($command);
    }

    public function getRawCommand()
    {
        return $this->rawCommand;
    }

    public function validate()
    {
        if (false === $this->isQuery()) {
            return parent::validate();
        }
    }

    protected function parse()
    {
        if (false === $this->isQuery()) {
            return parent::parse();
        }
    }

    protected function isQuery()
    {
        if (strpos(strtolower($this->rawCommand), 'select') === 0) {
            return true;
        }

        return false;
    }
}
