<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SessionNodeMoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:node:move');
        $this->setDescription('Move a node in the current session');
        $this->addArgument('srcAbsPath', null, InputArgument::REQUIRED, 'The root of the subgraph to be moved.');
        $this->addArgument('destAbsPath', null, InputArgument::REQUIRED, 'The location to which the subgraph is to be moved');
        $this->setHelp(<<<HERE
Moves the node at <info>srcAbsPath</info> (and its entire subgraph) to the new
location at <info>destAbsPath</info>.

This is a session-write command and therefor requires a save to dispatch
the change.

The identifiers of referenceable nodes must not be changed by a move.
The identifiers of non-referenceable nodes may change.

A ConstraintViolationException is thrown either immediately, on dispatch
or on persist, if performing this operation would violate a node type or
implementation-specific constraint. Implementations may differ on when
this validation is performed.

As well, a ConstraintViolationException will be thrown on persist if an
attempt is made to separately save either the source or destination
node.

Note that this behaviour differs from that of workspace::move
, which is a workspace-write command and therefore immediately dispatches
changes.

The <info>destAbsPath</info> provided must not have an index on its final element. If
ordering is supported by the node type of the parent node of the new
location, then the newly moved node is appended to the end of the child
node list.

This command cannot be used to move an individual property by itself. It
moves an entire node and its subgraph.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $srcAbsPath = $input->getArgument('srcAbsPath');
        $destAbsPath = $input->getArgument('destAbsPath');
        $session->move($srcAbsPath, $destAbsPath);
    }
}
