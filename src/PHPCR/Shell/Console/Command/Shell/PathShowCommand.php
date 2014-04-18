<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class PathShowCommand extends Command
{
    protected function configure()
    {
        $this->setName('shell:path:show');
        $this->setDescription('Print Working Directory (or path)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<comment>' . $this->getHelper('phpcr')->getSession()->getCwd() . '</comment>'
        );
    }
}
