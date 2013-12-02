<?php

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\Shell\Console\ShellQueryCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Command\AbstractSessionCommand;

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
        $cwd = $this->getApplication()->getCwd();

        // absolute path
        if (substr($path, 0, 1) == '/') {
            $newPath = $path;
        } elseif ($path == '..') {
            $newPath = dirname($cwd);
        } else {
            $newPath = sprintf('%s/%s', $cwd, $path);
        }

        $session->getNode($newPath);
        $this->getApplication()->setCwd($newPath);
        $output->writeln($newPath);
    }
}

