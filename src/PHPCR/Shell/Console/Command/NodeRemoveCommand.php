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

class NodeRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:remove');
        $this->setDescription('Remove the node at the current path');
        $this->setHelp(<<<HERE
Remove the current node
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $currentNode = $session->getCurrentNode();
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
