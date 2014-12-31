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
use Symfony\Component\Console\Input\InputOption;

class NodeCloneCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:clone');
        $this->setDescription('Clone a node (immediate)');
        $this->addArgument('srcPath', InputArgument::REQUIRED, 'Path to source node');
        $this->addArgument('destPath', InputArgument::REQUIRED, 'Path to destination node');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, copy from this workspace');
        $this->addOption('remove-existing', null, InputOption::VALUE_NONE, 'Remove existing nodes');
        $this->setHelp(<<<HERE
Clones the subgraph at the node <info>srcAbsPath</info> in
<info>srcWorkspace</info> to the new location at <info>destAbsPath</info> in
the current workspace.

This method does not assign new identifiers to the newly cloned nodes but
preserves the identifiers of their respective source nodes. This applies to
both referenceable and non-referenceable nodes.

If the <info>--remove-existing</info> option is set and an existing node in
this workspace (the destination workspace) has the same identifier as a node
being cloned from srcWorkspace, then the incoming node takes precedence, and
the existing node (and its subgraph) is removed. If
<info>--remove-existing</info> option is not set then an identifier collision
causes this method to throw an ItemExistsException and no changes are made.

If successful, the change is persisted immediately, there is no need to call
save.

The <info>destAbsPath</info> provided must not have an index on its final
element.  If it does then a RepositoryException is thrown.  If ordering is
supported by the node type of the parent node of the new location, then the new
clone of the node is appended to the end of the child node list.

This method cannot be used to clone just an individual property; it clones a
node and its subgraph.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $srcWorkspace = $input->getArgument('srcWorkspace');
        $srcAbsPath = $session->getAbsPath($input->getArgument('srcPath'));
        $destAbsPath = $session->getAbsTargetPath($srcAbsPath, $input->getArgument('destPath'));
        $removeExisting = $input->getOption('remove-existing');

        // todo: Check to ensure that source node has the referenceable mixin

        if (!$srcWorkspace) {
            $srcWorkspace = $session->getWorkspace()->getName();
        }

        $workspace = $session->getWorkspace();
        $workspace->cloneFrom($srcWorkspace, $srcAbsPath, $destAbsPath, $removeExisting);
    }
}
