<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;
use PHPCR\NamespaceException;

class NodeLifecycleFollowCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:lifecycle:follow');
        $this->setDescription('Causes the lifecycle state of this node to undergo the specified transition. NOT IMPLEMENTED');
        $this->addArgument('transition', InputArgument::REQUIRED, 'A state transition');
        $this->setHelp(<<<HERE
Causes the lifecycle state of the current node to undergo the specified
transition.

This command may change the value of the jcr:currentLifecycleState
property, in most cases it is expected that the implementation will
change the value to that of the passed transition parameter, though this
is an implementation-specific issue. If the jcr:currentLifecycleState
property is changed the change is persisted immediately, there is no
need to call save.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $currentNode = $session->getCurrentNode();
        $transition = $input->getArgument('transition');
        $currentNode->followLifecycleTransition($transition);
    }
}

