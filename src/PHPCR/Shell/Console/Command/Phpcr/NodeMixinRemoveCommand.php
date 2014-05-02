<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeMixinRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:mixin:remove');
        $this->setDescription('Remove the named mixin to the current node');
        $this->addArgument('mixinName', null, InputArgument::REQUIRED, null, 'The name of the mixin node type to be removeed');
        $this->setHelp(<<<HERE
Removes the specified mixin node type from this node and removes
mixinName from this node's jcr:mixinTypes property.

Both the semantic change in effective node type and the persistence of
the change to the jcr:mixinTypes  property occur on persist.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $mixinName = $input->getArgument('mixinName');
        $currentNode = $session->getCurrentNode();
        $currentNode->removeMixin($mixinName);
    }
}
