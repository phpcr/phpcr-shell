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

class SessionLoginCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('session:login');
        $this->setDescription('Login or (relogin) to a session');
        $this->addArgument('userId', InputArgument::REQUIRED, 'Unique identifier of user');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password');
        $this->addArgument('workspaceName', InputArgument::OPTIONAL, 'Optional workspace name');
        $this->setHelp(<<<HERE
Login to a session.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('userId');
        $password = $input->getArgument('password');
        $workspaceName = $input->getArgument('workspaceName');
        $this->get('phpcr.session_manager')->relogin($username, $password, $workspaceName);
    }
}
