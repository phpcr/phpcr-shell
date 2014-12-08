<?php

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\PathNotFoundException;
use PHPCR\Shell\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PathChangeCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('shell:path:change');
        $this->setDescription('Change the current path');
        $this->addArgument('path');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        try {
            $session->chdir($path);
            $output->writeln('<comment>' . $session->getCwd() . '</comment>');
        } catch (PathNotFoundException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
