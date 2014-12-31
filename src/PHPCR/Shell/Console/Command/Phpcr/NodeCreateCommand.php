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

class NodeCreateCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:create');
        $this->setDescription('Create a node at the current path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node to create');
        $this->addArgument('primaryNodeTypeName', InputArgument::OPTIONAL, 'Optional name of primary node type to use');
        $this->setHelp(<<<HERE
Creates a new node at the specified <info>path</info>

This is session-write method, meaning that the addition of the new node
is dispatched upon SessionInterface::save().

The <info>path</info> provided must not have an index on its final element,
otherwise a RepositoryException is thrown.

If ordering is supported by the node type of the parent node of the new
node then the new node is appended to the end of the child node list.

If <info>primaryNodeTypeName</info> is specified, this type will be used.

Otherwise the new node's primary node type will be determined by the
child node definitions in the node types of its parent.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $pathHelper = $this->get('helper.path');

        $path = $session->getAbsPath($input->getArgument('path'));
        $primaryNodeTypeName = $input->getArgument('primaryNodeTypeName');

        $parentPath = $pathHelper->getParentPath($path);
        $nodeName = $pathHelper->getNodeName($path);
        $parentNode = $session->getNode($parentPath);
        $parentNode->addNode($nodeName, $primaryNodeTypeName);
    }
}
