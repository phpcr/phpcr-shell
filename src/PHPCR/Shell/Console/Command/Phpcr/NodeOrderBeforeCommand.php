<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeOrderBeforeCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:order-before');
        $this->setDescription('Reorder a child node of the current node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('srcChildRelPath', InputArgument::REQUIRED, 'The relative path to the child node to be moved in the ordering');
        $this->addArgument('destChildRelPath', InputArgument::REQUIRED, 'The relative path to the child before which the node srcChildRelPath will be placed');
        $this->setHelp(<<<HERE
If this node supports child node ordering, this method inserts the child
node at <info>srcChildRelPath</info> into the child node list at the position
immediately before <info>destChildRelPath</info>

To place the node <info>srcChildRelPath</info> at the end of the list, a
destChildRelPath of null is used.

Note that (apart from the case where <info>destChildRelPath</info> is null) both of
these arguments must be relative paths of depth one, in other words they
are the names of the child nodes, possibly suffixed with an index.

If <info>srcChildRelPath</info> and <info>destChildRelPath</info> are the same, then no change is
made.

This is session-write method, meaning that a change made by this method
is dispatched on save.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $srcChildRelPath = $input->getArgument('srcChildRelPath');
        $destChildRelPath = $input->getArgument('destChildRelPath');
        $node = $session->getNode($path);
        $node->orderBefore($srcChildRelPath, $destChildRelPath);
    }
}
