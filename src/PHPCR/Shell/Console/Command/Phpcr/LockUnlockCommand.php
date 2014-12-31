<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\RepositoryInterface;

class LockUnlockCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('lock:unlock');
        $this->setDescription('Unlock the node at the given path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<HERE
Removes the lock on the node at path.

Also removes the properties jcr:lockOwner and jcr:lockIsDeep from that
node. As well, the corresponding lock token is removed from the set of
lock tokens held by the current Session.

Note that it is possible to unlock a node even if it is checked-in (the
lock-related properties will be changed despite the checked-in status).
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_LOCKING_SUPPORTED, true);
        $this->dequiresDescriptor('jackalope.not_implemented.get_lock');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $path = $session->getAbsPath($input->getArgument('path'));

        $lockManager->unlock($path);
    }
}
