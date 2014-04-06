<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class SessionRefreshCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:refresh');
        $this->setDescription('Refresh the current session');
        $this->addOption('keep-changes', null, InputOption::VALUE_NONE, 'Keep any changes that have been made in this session');
        $this->setHelp(<<<HERE
Reloads the current session.

If the <info>keep-changes</info> option is not given then this method discards
all pending changes currently recorded in this Session and returns all items to
reflect the current saved state. Outside a transaction this state is simply the
current state of persistent storage. Within a transaction, this state will
reflect persistent storage as modified by changes that have been saved but not
yet committed.

If <info>keep-changes</info> is true then pending change are not discarded but
items that do not have changes pending have their state refreshed to reflect
the current saved state, thus revealing changes made by other sessions.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $keepChanges = $input->getOption('keep-changes');

        $session->refresh($keepChanges);
    }
}
