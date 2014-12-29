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

class NodeTypeUnregisterCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node-type:unregister');
        $this->setDescription('Unregister a node type UNSUPPORTED / TODO');
        $this->addArgument('nodeTypeName', InputArgument::REQUIRED, 'The name of the node type to unregister');
        $this->setHelp(<<<HERE
Unregisters the specified node type
HERE
        );

        $this->dequiresDescriptor('jackalope.not_implemented.node_type.unregister');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $nodeTypeName = $input->getArgument('nodeTypeName');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();

        $nodeType = $nodeTypeManager->unregisterNodeTypes(array($nodeTypeName));
    }
}
