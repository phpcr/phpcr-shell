<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\RepositoryInterface;

class LockTokenAddCommand extends PhpcrShellCommand
{
    protected function configure()
    {
        $this->setName('lock:token:add');
        $this->setDescription('Add a lock token to the current session');
        $this->addArgument('lockToken', InputArgument::REQUIRED, 'Lock token');
        $this->setHelp(<<<HERE
Adds the specified lock token to the current Session.

Holding a lock token makes the current Session the owner of the lock
specified by that particular lock token.
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_LOCKING_SUPPORTED, true);
        $this->dequiresDescriptor('jackalope.not_implemented.lock.token');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        $lockToken = $input->getArgument('lockToken');

        $lockManager->addLockToken($lockToken);
    }
}
