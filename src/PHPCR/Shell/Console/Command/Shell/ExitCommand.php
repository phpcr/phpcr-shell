<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExitCommand extends Command
{
    public function configure()
    {
        $this->setName('shell:exit');
        $this->setDescription('Logout and quit the shell');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getHelper('phpcr')->getSession()->logout();
        $output->writeln('<info>Bye!</info>');
        exit(0);
    }
}
