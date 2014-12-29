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
use Symfony\Component\Console\Input\InputOption;

class NodeTypeLoadCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node-type:load');
        $this->setDescription('Load or create a node type');
        $this->addOption('update', null, InputOption::VALUE_NONE, 'Update existing node type');
        $this->addArgument('cndFile', InputArgument::REQUIRED, 'The name file containing the CND data');
        $this->setHelp(<<<HERE
This command allows to register node types in the repository that are defined
in a CND (Compact Namespace and Node Type Definition) file as used by jackrabbit.

Custom node types can be used to define the structure of content repository
nodes, like allowed properties and child nodes together with the namespaces
and their prefix used for the names of node types and properties.

If you use <info>--update</info> existing node type definitions will be overwritten
in the repository.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $cndFile = $input->getArgument('cndFile');
        $update = $input->getOption('update');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();

        if (!file_exists($cndFile)) {
            throw new \InvalidArgumentException(sprintf(
                'The CND file "%s" does not exist.', $cndFile
            ));
        }

        $cndData = file_get_contents($cndFile);
        $nodeTypeManager->registerNodeTypesCnd($cndData, $update);
    }
}
