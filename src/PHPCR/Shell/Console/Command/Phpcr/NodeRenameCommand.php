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
