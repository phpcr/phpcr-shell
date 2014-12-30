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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\RepositoryInterface;

class LockRefreshCommand extends BasePhpcrCommand
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
        $this->dequiresDescriptor('jackalope.not_implemented.get_lock');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $path = $input->getArgument('path');
        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $lock = $lockManager->getLock($node->getPath());
            $lock->refresh();
        }
    }
}
