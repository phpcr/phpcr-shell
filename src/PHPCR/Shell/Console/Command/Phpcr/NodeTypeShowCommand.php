<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;

class NodeTypeShowCommand extends Command
{
    protected function configure()
    {
        $this->setName('node-type:show');
        $this->setDescription('Show the CND of a node type');
        $this->addArgument('nodeTypeName', InputArgument::REQUIRED, 'The name of the node type to show');
        $this->setHelp(<<<HERE
Show the CND (Compact Node Definition) of a given node type.
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

        try {
            $nodeType = $nodeTypeManager->getNodeType($nodeTypeName);
        } catch (NoSuchNodeTypeException $e) {
            throw new \Exception(sprintf(
                'The node type "%s" does not exist'
            , $nodeTypeName));
        }
        $cndWriter = new CndWriter($namespaceRegistry);
        $out = $cndWriter->writeString(array($nodeType));
        $output->writeln(sprintf('<comment>%s</comment>', $out));
    }
}
