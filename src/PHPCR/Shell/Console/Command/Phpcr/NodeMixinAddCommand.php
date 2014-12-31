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

class NodeMixinAddCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:mixin:add');
        $this->setDescription('Add the named mixin to the node (can include wildcards)');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('mixinName', InputArgument::REQUIRED, 'The name of the mixin node type to be added');
        $this->setHelp(<<<HERE
Adds the mixin node type named <info>mixinName</info> to the node(s) inferred by the path.

If this node is already of type <info>mixinName</info> (either due to a previously
added mixin or due to its primary type, through inheritance) then this
method has no effect. Otherwise <info>mixinName</info> is added to this node's
jcr:mixinTypes property.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $mixinName = $input->getArgument('mixinName');

        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $node->addMixin($mixinName);
        }
    }
}
