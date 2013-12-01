<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use PHPCR\Shell\Console\Command\QueryCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Command\DoctrineDbalInitCommand;
use PHPCR\Shell\Console\Helper\DoctrineDbalHelper;
use PHPCR\Shell\Console\Helper\ShellHelper;
use Symfony\Component\Console\Command\Command;
use PHPCR\Shell\Console\Command\ShellCommand;
use PHPCR\Shell\Console\Input\ArgvInput;

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

    public function run()
    {
        parent::run();
    }

    protected function getCommandName($input)
    {
        return 'phpcr_shell';
    }
}

