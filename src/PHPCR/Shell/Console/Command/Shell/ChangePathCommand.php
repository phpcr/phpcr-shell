<?php

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\Shell\Console\ShellQueryCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Command\AbstractSessionCommand;
use PHPCR\ItemNotFoundException;
use PHPCR\PathNotFoundException;

class ChangePathCommand extends AbstractSessionCommand
{
    protected function configure()
    {
        $this->setName('cd');
        $this->setDescription('Change the current path');
        $this->addArgument('path');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $input->getArgument('path');
        try {
            $session->chdir($path);
            $output->writeln('<comment>' . $session->getCwd() . '</comment>');
        } catch (PathNotFoundException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}

