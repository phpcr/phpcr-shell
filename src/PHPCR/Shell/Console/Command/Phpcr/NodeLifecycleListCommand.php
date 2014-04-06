<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;
use PHPCR\NamespaceException;
use PHPCR\RepositoryInterface;

class NodeLifecycleListCommand extends PhpcrShellCommand
{
    protected function configure()
    {
        $this->setName('node:lifecycle:list');
        $this->setDescription('Returns the list of valid state transitions for this node. NOT IMPLEMENTED');
        $this->setHelp(<<<HERE
Returns the list of valid state transitions for this node.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_LIFECYCLE_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $currentNode = $session->getCurrentNode();
        $transitions = $currentNode->getAllowedLifecycleTransitions();

        foreach ($transitions as $transition) {
            $output->writeln('<info>' . $transition . '</info>');
        }
    }
}

