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
use Symfony\Component\Console\Input\InputOption;
use PHPCR\RepositoryInterface;

class LockLockCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('lock:lock');
        $this->setDescription('Lock the node at the given path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node to be locked');
        $this->addOption('deep', null, InputOption::VALUE_NONE, 'If given this lock will apply to this node and all its descendants; if not, it applies only to this node.');
        $this->addOption('session-scoped', null, InputOption::VALUE_NONE, 'If given, this lock expires with the current session; if not it expires when explicitly or automatically unlocked for some other reason');
        $this->addOption('timeout', null, InputOption::VALUE_REQUIRED, 'Desired lock timeout in seconds (servers are free to ignore this value). If not used lock will not timeout');
        $this->addOption('owner-info', null, InputOption::VALUE_REQUIRED, ' string containing owner information supplied by the client; servers are free to ignore this value. If none is specified, the implementation chooses one (i.e. user name of current backend authentication credentials');
        $this->setHelp(<<<HERE
Places a lock on the node at <info>path</info>.

If successful, the node is said to hold the lock.

If <info>deep</info> option is given then the lock applies to the specified node and
all its descendant nodes; if false, the lock applies only to the
specified node. On a successful lock, the jcr:lockIsDeep property of the
locked node is set to this value.

If <b>session-scoped</b> is specified then this lock will expire upon the
expiration of the current session (either through an automatic or
explicit <info>sesiion:logout</info>; if not given, this lock does not
expire until it is explicitly unlocked, it times out, or it is
automatically unlocked due to a implementation-specific limitation.

The <info>timeout</info> parameter specifies the number of seconds until the
lock times out (if it is not refreshed with LockInterface::refresh() in
the meantime). An implementation may use this information as a hint or
ignore it altogether. Clients can discover the actual timeout by
inspecting the returned Lock object.

The <info>ownerInfo</info> parameter can be used to pass a string holding
owner information relevant to the client. An implementation may either
use or ignore this parameter.

The addition or change of the properties jcr:lockIsDeep and
jcr:lockOwnerare persisted immediately; there is no need to call save.

It is possible to lock a node even if it is checked-in.
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
        $isDeep = $input->getOption('deep');
        $isSessionScoped = $input->getOption('session-scoped');
        $timeout = $input->getOption('timeout');
        $ownerInfo = $input->getOption('owner-info');

        $lockManager->lock($path, $isDeep, $isSessionScoped, $timeout, $ownerInfo);
    }
}
