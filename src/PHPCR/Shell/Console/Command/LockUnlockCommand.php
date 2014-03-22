<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LockUnlockCommand extends Command
{
    protected function configure()
    {
        $this->setName('lock:unlock');
        $this->setDescription('Unlock the node at the given path');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path of node to be unlocked');
        $this->setHelp(<<<HERE
Removes the lock on the node at absPath.

Also removes the properties jcr:lockOwner and jcr:lockIsDeep from that
node. As well, the corresponding lock token is removed from the set of
lock tokens held by the current Session.

If the node does not currently hold a lock or holds a lock for which
this Session is not the owner and is not a "lock-superuser", then a
\PHPCR\Lock\LockException is thrown.

<b>Note:</b>
However that the system may give permission to a non-owning session
to unlock a lock. Typically such "lock-superuser" capability is intended
to facilitate administrational clean-up of orphaned open-scoped locks.

Note that it is possible to unlock a node even if it is checked-in (the
lock-related properties will be changed despite the checked-in status).
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $absPath = $input->getArgument('absPath');

        $lockManager->unlock($absPath);
    }
}
