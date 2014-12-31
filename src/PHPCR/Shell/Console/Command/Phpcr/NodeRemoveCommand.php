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
use Symfony\Component\Console\Input\InputOption;

class NodeRemoveCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:remove');
        $this->setDescription('Remove the node at path (can include wildcards)');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addOption('shared', null, InputOption::VALUE_NONE, 'Remove nodes in shared set');
        $this->setHelp(<<<HERE
Remove the node at the given path.

If the `--shared` option is specified then any nodes within this nodes
shared-set (i.e. nodes which reference this node from other workspaces) will
also be removed (if the repository supports shared sets).
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $currentPath = $session->getCwd();
        $nodePaths = array();

        // verify that node exists by trying to get it..
        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            if ($node->getPath() == '/') {
                throw new \InvalidArgumentException(
                    'You cannot delete the root node!'
                );
            }

            $references = $node->getReferences();

            if (count($references) > 0) {
                $paths = array();
                foreach ($references as $reference) {
                    $paths[] = $reference->getPath();
                }

                throw new \InvalidArgumentException(sprintf(
                    'The node "%s" is referenced by the following properties: "%s"',
                    $node->getPath(),
                    implode('", "', $paths)
                ));
            }

            $nodePaths[] = $node->getPath();

            if ($input->getOption('shared')) {
                $node->removeSharedSet();
            } else {
                $node->remove();
            }
        }

        // if we deleted the current path, switch back to the parent node
        if (in_array($currentPath, $nodePaths)) {
            $session->chdir('..');
        }
    }
}
