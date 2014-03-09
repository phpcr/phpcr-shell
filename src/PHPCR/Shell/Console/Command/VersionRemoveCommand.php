<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class VersionRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('version:remove');
        $this->setDescription('Remove a node version');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $absPath = $input->getArgument('absPath');
        $workspace = $session->getWorkspace();

        $versionManager = $workspace->getVersionManager();
        $version = $versionManager->checkin($absPath);

        $output->writeln('Version: ' . $version);
    }
}
