<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:remove');
        $this->setDescription('Remove the node at path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<HERE
Remove the node at the given path.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $currentNode = $session->getNode($path);
        $currentPath = $currentNode->getPath();

        if ($currentPath == '/') {
            throw new \InvalidArgumentException(
                'Cannot delete root node!'
            );
        }

        $session->removeItem($currentPath);
        $session->chdir('..');
    }
}
