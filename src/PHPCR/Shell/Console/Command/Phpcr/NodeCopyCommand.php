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

class NodeCopyCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:copy');
        $this->setDescription('Copy a node (immediate)');
        $this->addArgument('srcPath', InputArgument::REQUIRED, 'Path to source node');
        $this->addArgument('destPath', InputArgument::REQUIRED, 'Path to destination node');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, copy from this workspace');
        $this->setHelp(<<<HERE
Copies a Node including its children to a new location to the given workspace.

This method copies the subgraph rooted at, and including, the node at
<info>srcWorkspace</info> (if given) and <info>srcAbsPath</info> to the new location in this
Workspace at <info>destAbsPath</info>.

This is a workspace-write operation and therefore dispatches changes
immediately and does not require a save.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $srcAbsPath = $session->getAbsPath($input->getArgument('srcPath'));
        $destAbsPath = $session->getAbsTargetPath($srcAbsPath, $input->getArgument('destPath'));
        $srcWorkspace = $input->getArgument('srcWorkspace');

        $workspace = $session->getWorkspace();

        $workspace->copy($srcAbsPath, $destAbsPath, $srcWorkspace);
    }
}
