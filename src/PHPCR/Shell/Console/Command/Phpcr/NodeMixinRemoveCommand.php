<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeMixinRemoveCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:mixin:remove');
        $this->setDescription('Remove the named mixin to the current node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('mixinName', InputArgument::REQUIRED, 'The name of the mixin node type to be removeed');
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
        $session = $this->get('phpcr.session');
        $mixinName = $input->getArgument('mixinName');
        $path = $input->getArgument('path');
        $currentNode = $session->getNodeByPathOrIdentifier($path);
        $currentNode->removeMixin($mixinName);
    }
}
