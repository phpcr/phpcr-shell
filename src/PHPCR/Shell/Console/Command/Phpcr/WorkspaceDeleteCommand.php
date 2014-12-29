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

class WorkspaceDeleteCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('workspace:delete');
        $this->setDescription('Delete a workspace');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of new workspace');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, clone from this workspace');
        $this->setHelp(<<<HERE
Deletes the workspace with the specified name from the repository,
deleting all content within it.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $name = $input->getArgument('name');

        $workspace = $session->getWorkspace();
        $workspace->deleteWorkspace($name);
    }
}
