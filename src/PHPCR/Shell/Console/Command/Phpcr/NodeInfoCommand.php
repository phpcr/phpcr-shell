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

use PHPCR\Shell\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NodeInfoCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:info');
        $this->setDescription('Show information about the current node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->setHelp(<<<'HERE'
Show information about the node(s) at the given path:

    PHPCRSH> node:info path/to/node

The path can include wildcards.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $nodeHelper = $this->get('helper.node');
        $formatter = $this->get('helper.result_formatter');

        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $mixins = $node->getMixinNodeTypes();
            $mixinNodeTypeNames = [];

            foreach ($mixins as $mixin) {
                $mixinNodeTypeNames[] = $mixin->getName();
            }

            if ($nodeHelper->nodeHasMixinType($node, 'mix:versionable')) {
                try {
                    $isCheckedOut = $node->isCheckedOut() ? 'yes' : 'no';
                } catch (\Exception $e) {
                    $isCheckedOut = $formatter->formatException($e);
                }
            } else {
                $isCheckedOut = 'N/A';
            }

            try {
                $isLocked = $node->isLocked() ? 'yes' : 'no';
            } catch (\Exception $e) {
                $isLocked = $formatter->formatException($e);
            }

            $info = [
                'UUID'              => $node->hasProperty('jcr:uuid') ? $node->getProperty('jcr:uuid')->getValue() : 'N/A',
                'Index'             => $node->getIndex(),
                'Primary node type' => $node->getPrimaryNodeType()->getName(),
                'Mixin node types'  => implode(', ', $mixinNodeTypeNames),
                'Checked out?'      => $isCheckedOut,
                'Locked?'           => $isLocked,
            ];

            $output->writeln('<pathbold>'.$node->getPath().'</pathbold>');
            $table = new Table($output);

            foreach ($info as $label => $value) {
                $table->addRow([$label, $value]);
            }

            $table->render($output);
        }
    }
}
