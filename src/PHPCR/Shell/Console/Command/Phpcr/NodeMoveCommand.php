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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeMoveCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:move');
        $this->setDescription('Move a node in the current session');
        $this->addArgument('srcPath', InputArgument::REQUIRED, 'The root of the subgraph to be moved.');
        $this->addArgument('destPath', InputArgument::REQUIRED, 'The location to which the subgraph is to be moved');
        $this->setHelp(<<<HERE
Moves the node at <info>srcPath</info> (and its entire subgraph) to the new
location at <info>destPath</info>.

This is a session-write command and therefor requires a save to dispatch
the change.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $srcPath = $input->getArgument('srcPath');
        $destPath = $input->getArgument('destPath');

        $session->move($srcPath, $destPath);
    }
}
