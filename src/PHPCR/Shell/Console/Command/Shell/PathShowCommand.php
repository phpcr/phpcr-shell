<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Command\BaseCommand;

class PathShowCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('shell:path:show');
        $this->setDescription('Print Working Directory (or path)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<comment>' . $this->get('phpcr.session')->getCwd() . '</comment>'
        );
    }
}
