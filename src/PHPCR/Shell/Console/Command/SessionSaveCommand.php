<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SessionSaveCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:save');
        $this->setDescription('Save the current session');
        $this->setHelp(<<<HERE
Validates all pending changes currently recorded in this Session.

If validation of all pending changes succeeds, then this change
information is cleared from the Session.

If the save occurs outside a transaction, the changes are dispatched and
persisted. Upon being persisted the changes become potentially visible
to other Sessions bound to the same persistent workspace.

If the save occurs within a transaction, the changes are dispatched but
are not persisted until the transaction is committed.

If validation fails, then no pending changes are dispatched and they
remain recorded on the Session. There is no best-effort or partial save.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();

        $session->save();
    }
}
