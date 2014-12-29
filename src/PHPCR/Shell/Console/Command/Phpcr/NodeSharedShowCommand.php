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
use PHPCR\RepositoryInterface;

class NodeSharedShowCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:shared:show');
        $this->setDescription('Show all the nodes are in the shared set of this node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node (can include wildcard)');
        $this->setHelp(<<<HERE
Lists all nodes that are in the shared set of this node.

Shareable nodes are analagous to symbolic links in a linux filesystem and can
be created by cloning a node within the same workspace.

If this node is not shared then only this node is shown.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_SHAREABLE_NODES_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $output->writeln('<path>' . $node->getPath() . '</path>');
            $sharedSet = $node->getSharedSet();

            foreach ($sharedSet as $sharedNode) {
                $output->writeln($sharedNode->getPath());
            }
        }
    }
}
