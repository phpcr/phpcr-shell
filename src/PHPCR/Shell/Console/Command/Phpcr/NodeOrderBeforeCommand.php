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

class NodeOrderBeforeCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:order-before');
        $this->setDescription('Reorder a child node of the current node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('srcChildRelPath', InputArgument::REQUIRED, 'The relative path to the child node to be moved in the ordering');
        $this->addArgument('destChildRelPath', InputArgument::REQUIRED, 'The relative path to the child before which the node srcChildRelPath will be placed');
        $this->setHelp(<<<HERE
This command is used to change the order of a child node relative to the current node.

For example, given that the node <path>/foobar</path> has the children <node>child2</node> and
<node>child4</node> then:

    PHPCRSH> cd foobar
    PHPCRSH> node-order . child4 child2

Will reorder "child4" before "child2".
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $srcChildRelPath = $input->getArgument('srcChildRelPath');
        $destChildRelPath = $input->getArgument('destChildRelPath');
        $node = $session->getNodeByPathOrIdentifier($path);
        $node->orderBefore($srcChildRelPath, $destChildRelPath);
    }
}
