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

class NodeTypeListCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node-type:list');
        $this->setDescription('List registered node types');
        $this->addArgument('filter', InputArgument::OPTIONAL, 'Perl regexp pattern');
        $this->setHelp(<<<HERE
List all node types (both primary and mixins) a filter can be optionally passed:

    PHPCRSH> node-type:list --filter mix:.*
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();
        $filter = $input->getArgument('filter');

        $nodeTypes = $nodeTypeManager->getAllNodeTypes();

        $table = $this->get('helper.table')->create();
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
                $nodeType->isQueryable() ? 'yes' : 'no',
            ));
        }

        $table->render($output);
    }
}
