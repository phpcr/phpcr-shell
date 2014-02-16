<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;

class SessionLogoutCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:logout');
        $this->setDescription('Logout of the current session');
        $this->setHelp(<<<HERE
Releases all resources associated with this Session.

This command should be called when a Session is no longer needed.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $session->logout();
    }
}



