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

class NodeDefinitionCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:definition');
        $this->setDescription('Show the CND Definition of current node NOT IMPLEMENTED');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<HERE
Show the CND definition of the primary type of the current node.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $currentNode = $session->getNode($path);
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $nodeType = $currentNode->getDefinition();
        $cndWriter = new CndWriter($namespaceRegistry);
        $out = $cndWriter->writeString(array($nodeType));
        $output->writeln(sprintf('<comment>%s</comment>', $out));
    }
}
