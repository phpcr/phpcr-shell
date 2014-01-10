<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use PHPCR\Shell\Console\Command\ShellCommand;

/**
 * This application wraps a single command which accepts
 * the connection parameters and starts an interactive shell.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SessionApplication extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('PHPCR', '1.0');

        $command = new ShellCommand();
        $command->setApplication($this);
        $this->add($command);
    }

    public function getDefaultInputDefinition()
    {
        return new InputDefinition(array());
    }

    protected function getCommandName(InputInterface $input)
    {
        return 'phpcr_shell';
    }
}

