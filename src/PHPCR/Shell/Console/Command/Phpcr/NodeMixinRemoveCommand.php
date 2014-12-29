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

class NodeMixinRemoveCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:mixin:remove');
        $this->setDescription('Remove the named mixin to the current node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node (can include wildcards)');
        $this->addArgument('mixinName', InputArgument::REQUIRED, 'The name of the mixin node type to be removeed');
        $this->setHelp(<<<HERE
Removes the specified mixin node type from this node and removes
mixinName from this node's jcr:mixinTypes property.

Both the semantic change in effective node type and the persistence of
the change to the jcr:mixinTypes  property occur on persist.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $mixinName = $input->getArgument('mixinName');
        $path = $input->getArgument('path');

        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $node->removeMixin($mixinName);
        }
    }
}
