<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeTypeUnregisterCommand extends Command
{
    protected function configure()
    {
        $this->setName('node-type:unregister');
        $this->setDescription('Unregister a node type UNSUPPORTED / TODO');
        $this->addArgument('nodeTypeName', null, InputArgument::REQUIRED, 'The name of the node type to unregister');
        $this->setHelp(<<<HERE
Unregisters the specified node type
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $nodeTypeName = $input->getArgument('nodeTypeName');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();

        $nodeType = $nodeTypeManager->unregisterNodeTypes(array($nodeTypeName));
    }
}
