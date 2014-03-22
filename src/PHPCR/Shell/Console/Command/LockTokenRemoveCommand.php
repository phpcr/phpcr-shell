<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LockTokenRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('lock:token:remove');
        $this->setDescription('Remove a lock token to the current session');
        $this->addArgument('lockToken', InputArgument::REQUIRED, 'Lock token');
        $this->setHelp(<<<HERE
Removes the specified lock token from the current Session.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        $lockToken = $input->getArgument('lockToken');

        $lockManager->removeLockToken($lockToken);
    }
}
