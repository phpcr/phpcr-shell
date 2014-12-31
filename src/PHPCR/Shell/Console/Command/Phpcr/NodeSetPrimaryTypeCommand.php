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

class NodeSetPrimaryTypeCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:set-primary-type');
        $this->setDescription('Set the primary type of the current node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node (can include wildcard)');
        $this->addArgument('nodeTypeName', InputArgument::REQUIRED, 'New primary node type name');
        $this->setHelp(<<<HERE
Changes the primary node type of this node to nodeTypeName.

Also immediately changes this node's jcr:primaryType property
appropriately.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $nodeTypeName = $input->getArgument('nodeTypeName');

        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $node->setPrimaryType($nodeTypeName);
        }
    }
}
