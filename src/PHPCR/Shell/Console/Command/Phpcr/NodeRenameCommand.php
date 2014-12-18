<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeRenameCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:rename');
        $this->setDescription('Rename the node at the current path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('newName', InputArgument::REQUIRED, 'The name of the node to create');
        $this->setHelp(<<<HERE
Renames this node to the specified <info>newName</info>. The ordering (if any) of
this node among it siblings remains unchanged.

This is a session-write method, meaning that the name change is
dispatched upon <comment>session:save</comment>.

The <info>newName</info> provided must not have an index, otherwise a
RepositoryException is thrown.

An ItemExistsException will be thrown either immediately, on dispatch
(save, whether within or without transactions) or on persist (save
without transactions, commit within a transaction), if there already
exists a sibling item of this node with the specified name and
same-name-siblings are not allowed. Implementations may differ on when
this validation is performed.

A ConstraintViolationException will be thrown either immediately, on
dispatch (save, whether within or without transactions) or on persist
(save without transactions, commit within a transaction), if changing
the name would violate a node type or implementation-specific
constraint. Implementations may differ on when this validation is
performed.

A VersionException will be thrown either immediately, on dispatch (save,
whether within or without transactions) or on persist (save without
transactions, commit within a transaction), if this node is read-only
due to a checked-in node. Implementations may differ on when this
validation is performed.

A LockException will be thrown either immediately, on dispatch (save,
whether within or without transactions) or on persist (save without
transactions, commit within a transaction), if a lock prevents the name
change of the node. Implementations may differ on when this validation
is performed.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $newName = $input->getArgument('newName');
        $currentNode = $session->getNodeByPathOrIdentifier($path);
        $currentNode->rename($newName);
    }
}
