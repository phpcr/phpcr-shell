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

class WorkspaceCreateCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('workspace:create');
        $this->setDescription('Create a new workspace');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of new workspace');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, clone from this workspace');
        $this->setHelp(<<<HERE
Creates a new Workspace with the specified name. The new workspace is
empty, meaning it contains only root node.

If <info>srcWorkspace</info> is given, then it
creates a new Workspace with the specified name initialized with a
clone of the content of the workspace srcWorkspace. Semantically,
this command is equivalent to creating a new workspace and manually
cloning <info>srcWorkspace</info> to it.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $name = $input->getArgument('name');
        $srcWorkspace = $input->getArgument('srcWorkspace');

        $workspace = $session->getWorkspace();
        $workspace->createWorkspace($name, $srcWorkspace);
    }
}
