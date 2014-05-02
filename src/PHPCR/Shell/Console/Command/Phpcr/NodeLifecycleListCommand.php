<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\RepositoryInterface;

class NodeLifecycleListCommand extends PhpcrShellCommand
{
    protected function configure()
    {
        $this->setName('node:lifecycle:list');
        $this->setDescription('Returns the list of valid state transitions for this node.');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<HERE
Returns the list of valid state transitions for this node.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_LIFECYCLE_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $currentNode = $session->getNode($path);
        $transitions = $currentNode->getAllowedLifecycleTransitions();

        foreach ($transitions as $transition) {
            $output->writeln('<info>' . $transition . '</info>');
        }
    }
}
