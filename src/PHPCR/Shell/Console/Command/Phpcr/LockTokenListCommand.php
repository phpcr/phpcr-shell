<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\RepositoryInterface;

class LockTokenListCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('lock:token:list');
        $this->setDescription('List a lock token to the current session');
        $this->setHelp(<<<HERE
Show a list of previously registered tokens.

Displays all lock tokens currently held by the
current Session. Note that any such tokens will represent open-scoped
locks, since session-scoped locks do not have tokens.
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_LOCKING_SUPPORTED, true);
        $this->dequiresDescriptor('jackalope.not_implemented.lock.token');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();

        $lockTokens = $lockManager->getLockTokens();

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Token'));

        foreach ($lockTokens as $token) {
            $table->addRow(array($token));
        }

        $table->render($output);
    }
}
