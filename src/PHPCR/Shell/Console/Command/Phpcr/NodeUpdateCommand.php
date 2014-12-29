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

class NodeUpdateCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:update');
        $this->setDescription('Updates a node corresponding to the given path in the given workspace');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node (can include wildcards)');
        $this->addArgument('srcWorkspace', InputArgument::REQUIRED, 'The name of the source workspace');
        $this->setHelp(<<<HERE
Updates a node corresponding to the current one in the given workspace.

If this node does have a corresponding node in the workspace
srcWorkspace, then this replaces this node and its subgraph with a clone
of the corresponding node and its subgraph.
If this node does not have a corresponding node in the workspace
srcWorkspace, then the update method has no effect.

If the update succeeds the changes made are persisted immediately, there
is no need to call save.

Note that update does not respect the checked-in status of nodes. An
update may change a node even if it is currently checked-in (This fact
is only relevant in an implementation that supports versioning).
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');

        $srcWorkspace = $input->getArgument('srcWorkspace');

        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $output->writeln('<path>' . $node->getPath() . '</path>');
            $node->update($srcWorkspace);
        }
    }
}
