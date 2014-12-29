<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;

class NodeDefinitionCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:definition');
        $this->setDescription('Show the CND Definition of specified node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<HERE
Show the CND definition of the primary type of the current node.
HERE
        );

        $this->dequiresDescriptor('jackalope.not_implemented.node.definition');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $currentNode = $session->getNodeByPathOrIdentifier($path);
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $nodeType = $currentNode->getDefinition();
        $cndWriter = new CndWriter($namespaceRegistry);
        $out = $cndWriter->writeString(array($nodeType));
        $output->writeln(sprintf('<comment>%s</comment>', $out));
    }
}
