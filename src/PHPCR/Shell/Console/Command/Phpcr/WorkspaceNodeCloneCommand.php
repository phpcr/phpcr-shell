<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class WorkspaceNodeCloneCommand extends Command
{
    protected function configure()
    {
        $this->setName('workspace:node:clone');
        $this->setDescription('Copy a node from one workspace to another');
        $this->addArgument('srcWorkspace', InputArgument::REQUIRED, 'If specified, copy from this workspace');
        $this->addArgument('srcAbsPath', InputArgument::REQUIRED, 'Absolute path to source node');
        $this->addArgument('destAbsPath', InputArgument::REQUIRED, 'Absolute path to destination node');
        $this->addOption('remove-existing', null, InputOption::VALUE_NONE, 'Remove existing nodes');
        $this->setHelp(<<<HERE
Clones the subgraph at the node <info>srcAbsPath</info> in
<info>srcWorkspace</info> to the new location at <info>destAbsPath</info> in
the current workspace.

Unlike the signature of copy that copies between workspaces, this method does
not assign new identifiers to the newly cloned nodes but preserves the
identifiers of their respective source nodes. This applies to both
referenceable and non-referenceable nodes.

In some implementations there may be cases where preservation of a
non-referenceable identifier is not possible, due to how non-referenceable
identifiers are constructed in that implementation. In such a case this method
will throw a RepositoryException.

If the <info>--remove-existing</info> option is set and an existing node in
this workspace (the destination workspace) has the same identifier as a node
being cloned from srcWorkspace, then the incoming node takes precedence, and
the existing node (and its subgraph) is removed. If
<info>--remove-existing<info> option is not set then an identifier collision
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
        $session = $this->getHelper('phpcr')->getSession();
        $srcWorkspace = $input->getArgument('srcWorkspace');
        $srcAbsPath = $input->getArgument('srcAbsPath');
        $destAbsPath = $input->getArgument('destAbsPath');
        $removeExisting = $input->getOption('remove-existing');

        // todo: Check to ensure that source node has the referenceable mixin

        $workspace = $session->getWorkspace();
        $workspace->cloneFrom($srcWorkspace, $srcAbsPath, $destAbsPath, $removeExisting);
    }
}
