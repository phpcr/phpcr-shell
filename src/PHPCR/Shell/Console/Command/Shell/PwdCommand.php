<?php

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\Shell\Console\ShellQueryCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Command\AbstractSessionCommand;

class PwdCommand extends AbstractSessionCommand
{
    protected function configure()
    {
        $this->setName('pwd');
        $this->setDescription('Print Working Directory (or path)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<comment>' . $this->getHelper('phpcr')->getSession()->getCwd() . '</comment>'
        );
    }
}


