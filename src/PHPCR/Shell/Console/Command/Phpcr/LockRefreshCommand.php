<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\RepositoryInterface;

class LockRefreshCommand extends PhpcrShellCommand
{
    protected function configure()
    {
        $this->setName('lock:refresh');
        $this->setDescription('Refresh the TTL of the lock of the node at the given path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node containing the lock to be refreshed');
        $this->setHelp(<<<HERE
If this lock's time-to-live is governed by a timer, this command resets
that timer so that the lock does not timeout and expire.

If this lock's time-to-live is not governed by a timer, then this method
has no effect.
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_LOCKING_SUPPORTED, true);
        $this->dequiresDescriptor('jackalope.not_implemented.lock.refresh');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $path = $session->getAbsPath($input->getArgument('path'));

        $lock = $lockManager->getLock($path);
        $lock->refresh();
    }
}
