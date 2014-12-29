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

class WorkspaceUseCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('workspace:use');
        $this->setDescription('Change the current workspace');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of workspace to use');
        $this->addArgument('srcWorkspace', InputArgument::OPTIONAL, 'If specified, clone from this workspace');
        $this->setHelp(<<<HERE
Change the workspace.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workspaceName = $input->getArgument('name');
        $this->get('phpcr.session_manager')->changeWorkspace($workspaceName);
    }
}
