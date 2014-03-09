<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class WorkspaceDeleteCommand extends Command
{
    protected function configure()
    {
        $this->setName('workspace:delete');
        $this->setDescription('Delete a new workspace');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of new workspace');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, clone from this workspace');
        $this->setHelp(<<<HERE
Deletes the workspace with the specified name from the repository,
deleting all content within it.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $name = $input->getArgument('name');

        $workspace = $session->getWorkspace();
        $workspace->deleteWorkspace($name);
    }
}
