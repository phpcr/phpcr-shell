<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class WorkspaceChangeCommand extends Command
{
    protected function configure()
    {
        $this->setName('workspace-change');
        $this->addArgument('workspace');
        $this->setDescription('Change to a different workspace');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
    }
}



