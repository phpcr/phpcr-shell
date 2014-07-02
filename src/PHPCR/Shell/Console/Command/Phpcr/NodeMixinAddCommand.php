<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeMixinAddCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:mixin:add');
        $this->setDescription('Add the named mixin to the node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('mixinName', InputArgument::REQUIRED, 'The name of the mixin node type to be added');
        $this->setHelp(<<<HERE
Adds the mixin node type named <info>mixinName</info> to this node.

If this node is already of type <info>mixinName</info> (either due to a previously
added mixin or due to its primary type, through inheritance) then this
method has no effect. Otherwise <info>mixinName</info> is added to this node's
jcr:mixinTypes property.

Semantically, the new node type may take effect immediately, on dispatch
or on persist. The behavior is adopted must be the same as the behavior
adopted for NodeInterface::setPrimaryType() and the behavior that
occurs when a node is first created.

A ConstraintViolationException is thrown either immediately or on save
if a conflict with another assigned mixin or the primary node type
occurs or for an implementation-specific reason. Implementations may
differ on when this validation is done.

In some implementations it may only be possible to add mixin types
before a a node is persisted for the first time. In such cases any
later calls to <info>addMixin</info> will throw a ConstraintViolationException
either immediately, on dispatch or on persist.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $mixinName = $input->getArgument('mixinName');
        $currentNode = $session->getNode($path);
        $currentNode->addMixin($mixinName);
    }
}
