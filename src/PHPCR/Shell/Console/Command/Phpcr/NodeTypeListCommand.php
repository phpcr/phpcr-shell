<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NodeTypeListCommand extends Command
{
    protected function configure()
    {
        $this->setName('node-type:list');
        $this->setDescription('List registered node types');
        $this->addArgument('filter', InputArgument::OPTIONAL, 'Perl regexp pattern');
        $this->setHelp(<<<HERE
List all node types (both primary and mixins)
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();
        $filter = $input->getArgument('filter');

        $nodeTypes = $nodeTypeManager->getAllNodeTypes();

        $table = clone $this->getHelper('table');
        $table->setHeaders(array('Name', 'Primary Item Name', 'Abstract?', 'Mixin?', 'Queryable?'));

        foreach ($nodeTypes as $nodeType) {
            if ($filter && !preg_match('{' . $filter . '}', $nodeType->getName())) {
                continue;
            }

            $table->addRow(array(
                $nodeType->getName(),
                $nodeType->getPrimaryItemName(),
                $nodeType->isAbstract() ? 'yes' : 'no',
                $nodeType->isMixin() ? 'yes' : 'no',
                $nodeType->isQueryable() ? 'yes': 'no',
            ));
        }

        $table->render($output);
    }
}
