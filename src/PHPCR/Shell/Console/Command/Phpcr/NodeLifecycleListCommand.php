<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use PHPCR\RepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NodeLifecycleListCommand extends BasePhpcrCommand
{
    protected function configure(): void
    {
        $this->setName('node:lifecycle:list');
        $this->setDescription('Returns the list of valid state transitions for this node.');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(
            <<<'HERE'
Returns the list of valid state transitions for this node.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_LIFECYCLE_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $currentNode = $session->getNodeByPathOrIdentifier($path);
        $transitions = $currentNode->getAllowedLifecycleTransitions();

        foreach ($transitions as $transition) {
            $output->writeln('<info>'.$transition.'</info>');
        }

        return 0;
    }
}
