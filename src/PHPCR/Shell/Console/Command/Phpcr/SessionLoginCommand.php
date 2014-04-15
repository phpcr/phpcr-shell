<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SessionLoginCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:login');
        $this->setDescription('Login or (relogin) to a session');
        $this->addArgument('userId', InputArgument::REQUIRED, 'Unique identifier of user');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password');
        $this->addArgument('workspaceName', InputArgument::OPTIONAL, 'Optional workspace name');
        $this->setHelp(<<<HERE
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('userId');
        $password = $input->getArgument('password');
        $workspaceName = $input->getArgument('workspaceName');
        $this->getApplication()->relogin($username, $password, $workspaceName);
    }
}
