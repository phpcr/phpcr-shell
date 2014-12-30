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

class LockTokenRemoveCommand extends BasePhpcrCommand
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
        $this->requiresDescriptor(RepositoryInterface::OPTION_LOCKING_SUPPORTED, true);
        $this->dequiresDescriptor('jackalope.not_implemented.lock_token');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        $lockToken = $input->getArgument('lockToken');

        $lockManager->removeLockToken($lockToken);
    }
}
