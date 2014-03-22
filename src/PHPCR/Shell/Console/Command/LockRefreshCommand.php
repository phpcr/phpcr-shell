<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LockRefreshCommand extends Command
{
    protected function configure()
    {
        $this->setName('lock:refresh');
        $this->setDescription('Refresh the TTL of the lock of the node at the given path');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path of node containing the lock to be refreshed');
        $this->setHelp(<<<HERE
If this lock's time-to-live is governed by a timer, this command resets
that timer so that the lock does not timeout and expire.

If this lock's time-to-live is not governed by a timer, then this method
has no effect.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $absPath = $input->getArgument('absPath');

        $lock = $lockManager->getLock($absPath);
        $lock->refresh();
    }
}
