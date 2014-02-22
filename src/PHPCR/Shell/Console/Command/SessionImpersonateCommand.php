<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;
use PHPCR\SimpleCredentials;

class SessionImpersonateCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:impersonate');
        $this->setDescription('Impersonate the given user NOT SUPPORTED');
        $this->addArgument('username', InputArgument::REQUIRED, 'Username of user to impersonate');
        $this->setHelp(<<<HERE
Note: This command is not implemented by any of the transports currently.

Returns a new session in accordance with the specified (new)
Credentials.

Allows the current user to acquire a new session using incomplete or
relaxed credentials requirements (perhaps including a user name but no
password, for example), assuming that this Session gives them that
permission. This method can be used to "impersonate" another user or to
clone the current session by passing in the same credentials that were
used to acquire the current session.

The new Session is tied to a new Workspace instance. In other words,
Workspace instances are not re-used. However, the Workspace instance
returned represents the same actual persistent workspace entity in the
repository as is represented by the Workspace object tied to the current
Session.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $username = $input->getArgument('username');

        $credentials = new SimpleCredentials($username, '');
        $session->impersonate($credentials);
    }
}


