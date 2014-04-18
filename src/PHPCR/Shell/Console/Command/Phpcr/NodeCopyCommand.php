<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeCopyCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:copy');
        $this->setDescription('Copy a node');
        $this->addArgument('srcPath', InputArgument::REQUIRED, 'Path to source node');
        $this->addArgument('destPath', InputArgument::REQUIRED, 'Path to destination node');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, copy from this workspace');
        $this->setHelp(<<<HERE
Copies a Node including its children to a new location to the given workspace.

This method copies the subgraph rooted at, and including, the node at
<info>srcWorkspace</info> (if given) and <info>srcAbsPath</info> to the new location in this
Workspace at <info>destAbsPath</info>.

This is a workspace-write operation and therefore dispatches changes
immediately and does not require a save.

When a node N is copied to a path location where no node currently
exists, a new node N' is created at that location.
The subgraph rooted at and including N' (call it S') is created and is
identical to the subgraph rooted at and including N (call it S) with the
following exceptions:
- Every node in S' is given a new and distinct identifier
  - or, if <info>srcWorkspace</info> is given -
  Every referenceable node in S' is given a new and distinct identifier
  while every non-referenceable node in S' may be given a new and
  distinct identifier.
- The repository may automatically drop any mixin node type T present on
  any node M in S. Dropping a mixin node type in this context means that
  while M remains unchanged, its copy M' will lack the mixin T and any
  child nodes and properties defined by T that are present on M. For
  example, a node M that is mix:versionable may be copied such that the
  resulting node M' will be a copy of N except that M' will not be
  mix:versionable and will not have any of the properties defined by
  mix:versionable. In order for a mixin node type to be dropped it must
  be listed by name in the jcr:mixinTypes property of M. The resulting
  jcr:mixinTypes property of M' will reflect any change.
- If a node M in S is referenceable and its mix:referenceable mixin is
  not dropped on copy, then the resulting jcr:uuid property of M' will
  reflect the new identifier assigned to M'.
- Each REFERENCE or WEAKEREFERENCE property R in S is copied to its new
  location R' in S'. If R references a node M within S then the value of
  R' will be the identifier of M', the new copy of M, thus preserving the
  reference within the subgraph.

When a node N is copied to a location where a node N' already exists, the
repository may either immediately throw an ItemExistsException or attempt
to update the node N' by selectively replacing part of its subgraph with
a copy of the relevant part of the subgraph of N. If the node types of N
and N' are compatible, the implementation supports update-on-copy for
these node types and no other errors occur, then the copy will succeed.
Otherwise an ItemExistsException is thrown.

Which node types can be updated on copy and the details of any such
updates are implementation-dependent. For example, some implementations
may support update-on-copy for mix:versionable nodes. In such a case the
versioning-related properties of the target node would remain unchanged
(jcr:uuid, jcr:versionHistory, etc.) while the substantive content part
of the subgraph would be replaced with that of the source node.

The <info>destAbsPath</info> provided must not have an index on its final element. If
it does then a RepositoryException is thrown. Strictly speaking, the
<info>destAbsPath</info> parameter is actually an absolute path to the parent node of
the new location, appended with the new name desired for the copied node.
It does not specify a position within the child node ordering. If ordering
is supported by the node type of the parent node of the new location, then
the new copy of the node is appended to the end of the child node list.

This method cannot be used to copy an individual property by itself. It
copies an entire node and its subgraph (including, of course, any
properties contained therein).
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $srcAbsPath = $session->getAbsPath($input->getArgument('srcPath'));
        $destAbsPath = $session->getAbsPath($input->getArgument('destPath'));
        $srcWorkspace = $input->getArgument('srcWorkspace');

        $workspace = $session->getWorkspace();

        $workspace->copy($srcAbsPath, $destAbsPath, $srcWorkspace);
    }
}
