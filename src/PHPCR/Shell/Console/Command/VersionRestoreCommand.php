<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class VersionRestoreCommand extends Command
{
    protected function configure()
    {
        $this->setName('version:restore');
        $this->setDescription('Restore a node version');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to node');
        $this->addArgument('versionName', null, InputArgument::REQUIRED, 'Name of version to retore');
        $this->addOption('remove-existing', null, InputOption::VALUE_NONE, 'Flag that governs what happens in case of identifier collision');
        $this->setHelp(<<<HERE
Attempt to restore an old version of a node.

<em>If <info>absPath</info> is given and <info>versionName</info> is a version name:</em>
 Restores the node at <info>absPath</info> to the state defined by the version with
 the specified version name (<info>versionName</info>).
 This method will work regardless of whether the node at absPath is
 checked-in or not.


<em>If <info>absPath</info> is given and <info>versionName</info> is a VersionInterface instance:
</em>
 Restores the specified version to <info>absPath</info>. There must be no existing
 node at <info>absPath</info>. If one exists, a VersionException is thrown.
 There must be a parent node to the location at <info>absPath</info>, otherwise a
 PathNotFoundException is thrown.
 If the would-be parent of the location <info>absPath</info> is actually a property,
 or if a node type restriction would be violated, then a
 ConstraintViolationException is thrown.


<em>If <info>versionName</info> is VersionInterface instance:</em>
 Restores the node in the current workspace that is the versionable node
 of the specified version to the state reflected in that version.
 This method ignores checked-in status.


<em>If <info>versionName</info> is an array of VersionInterface instances:</em>
 Restores a set of versions at once. Used in cases where a "chicken and
 egg" problem of mutually referring REFERENCE properties would prevent
 the restore in any serial order.
 The following restrictions apply to the set of versions specified: If S
 is the set of versions being restored simultaneously,
 - For every version V in S that corresponds to a missing node, there
   must also be a parent of V in S.
 - S must contain at least one version that corresponds to an existing
   node in the workspace.
 - No V in S can be a root version (jcr:rootVersion).
 If any of these restrictions does not hold, the restore will fail
 because the system will be unable to determine the path locations to
 which one or more versions are to be restored. In this case a
 VersionException is thrown.
 The versionable nodes in the current workspace that correspond to the
 versions being restored define a set of (one or more) subgraphs.

<em>If the restore succeeds the changes made are dispatched immediately;
</em>
there is no need to call save.

If an array of VersionInterface instances is restored, an identifier
collision occurs when the current workspace contains a node outside these
subgraphs that has the same identifier as one of the nodes that would be
introduced by the restore operation into one of these subgraphs.
Else, an identifier collision occurs when a node exists outside the
subgraph rooted at absPath with the same identifier as a node that would
be introduced by the restore operation into the affected subgraph.
The result in such a case is governed by the removeExisting flag. If
<info>removeExisting</info> is true, then the incoming node takes precedence, and the
existing node (and its subgraph) is removed (if possible; otherwise a
RepositoryException is thrown). If <info>removeExisting</info> is false, then an
ItemExistsException is thrown and no changes are made. Note that this
applies not only to cases where the restored node itself conflicts with
an existing node but also to cases where a conflict occurs with any node
that would be introduced into the workspace by the restore operation. In
particular, conflicts involving subnodes of the restored node that have
OnParentVersion settings of COPY or VERSION are also governed by the
<info>removeExisting</info> flag.

<b>Note:</b> The Java API defines this with multiple differing
signatures, you need to act accordingly in your implementation.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $absPath = $input->getArgument('absPath');
        $versionName = $input->getArgument('versionName');
        $removeExisting = $input->getOption('remove-existing');
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $versionManager = $workspace->getVersionManager();
        $versionManager->restore($removeExisting, $versionName, $absPath);
    }
}
