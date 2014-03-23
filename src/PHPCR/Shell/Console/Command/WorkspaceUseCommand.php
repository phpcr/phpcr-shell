<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class WorkspaceUseCommand extends Command
{
    protected function configure()
    {
        $this->setName('workspace:use');
        $this->setDescription('Change the current workspace');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of workspace to use');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, clone from this workspace');
        $this->setHelp(<<<HERE
Change the workspace.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workspaceName = $input->getArgument('name');
        $this->getApplication()->changeWorkspace($workspaceName);
    }
}
