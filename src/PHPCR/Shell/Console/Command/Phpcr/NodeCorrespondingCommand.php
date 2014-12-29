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

class NodeCorrespondingCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:corresponding');
        $this->setDescription('Show the path for the current nodes corresponding path in named workspace');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('workspaceName', InputArgument::REQUIRED, 'The name of the workspace');
        $this->setHelp(<<<HERE
Returns the absolute path of the node in the specified workspace that
corresponds to this node.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $workspaceName = $input->getArgument('workspaceName');
        $currentNode = $session->getNodeByPathOrIdentifier($path);
        $correspondingPath = $currentNode->getCorrespondingNodePath($workspaceName);
        $output->writeln($correspondingPath);
    }
}
