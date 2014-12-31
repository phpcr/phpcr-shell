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

class VersionCheckinCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('version:checkin');
        $this->setDescription('Checkin (commit) a node version');
        $this->addArgument('path', InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
Creates for the versionable node at <info>path</info> a new version with a system
generated version name and returns that version (which will be the new
base version of this node). Sets the <property>jcr:checkedOut</property> property to false
thus putting the node into the checked-in state. This means that the node
and its connected non-versionable subgraph become read-only. A node's
connected non-versionable subgraph is the set of non-versionable descendant
nodes reachable from that node through child links without encountering
any versionable nodes. In other words, the read-only status flows down
from the checked-in node along every child link until either a versionable
node is encountered or an item with no children is encountered. In a
system that supports only simple versioning the connected non-versionable
subgraph will be equivalent to the whole subgraph, since simple-versionable
nodes cannot have simple-versionable descendants.

Read-only status means that an item cannot be altered by the client using
standard API methods (addNode, setProperty, etc.). The only exceptions to
this rule are the restore(), restoreByLabel(), merge() and Node::update()
operations; these do not respect read-only status due to check-in. Note
that remove of a read-only node is possible, as long as its parent is not
read-only (since removal is an alteration of the parent node).

If this node is already checked-in, this method has no effect but returns
the current base version of this node.

If checkin succeeds, the change to the <property>jcr:isCheckedOut</property> property is
dispatched immediately.
HERE
    );

        $this->requiresDescriptor(RepositoryInterface::OPTION_VERSIONING_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $nodeHelper = $this->get('helper.node');
        $path = $input->getArgument('path');
        $workspace = $session->getWorkspace();

        $versionManager = $workspace->getVersionManager();

        $node = $session->getNodeByPathOrIdentifier($path);
        $nodeHelper->assertNodeIsVersionable($node);

        $version = $versionManager->checkin($path);

        $output->writeln('Version: ' . $version->getName());
    }
}
