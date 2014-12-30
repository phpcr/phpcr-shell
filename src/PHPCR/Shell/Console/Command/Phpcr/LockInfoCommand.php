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

class LockInfoCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('lock:info');
        $this->setDescription('Show details of the lock that applies to the specified node path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of locked node');
        $this->setHelp(<<<HERE
Shows the details of the lock that applies to the node at the specified
path.

This may be either of the lock on that node itself or a deep lock on a node
above that node.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_LOCKING_SUPPORTED, true);
        $this->dequiresDescriptor('jackalope.not_implemented.get_lock');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $session->getAbsPath($input->getArgument('path'));
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $lock = $lockManager->getLock($path);

        $info = array(
            'Lock owner' => $lock->getLockOwner(),
            'Lock token' => $lock->getLockToken(),
            'Seconds remaining' => $lock->getSecondsRemaining(),
            'Deep?' => $lock->isDeep() ? 'yes' : 'no',
            'Live?' => $lock->isLove() ? 'yes' : 'no',
            'Owned by current session?' => $lock->isLockOwningSession() ? 'yes' : 'no',
            'Session scoped?' => $lock->isSessionScoped() ? 'yes' : 'no',
        );

        $table = $this->get('helper.table')->create();

        foreach ($info as $label => $value) {
            $table->addRow(array($label, $value));
        }

        $table->render($output);
    }
}
