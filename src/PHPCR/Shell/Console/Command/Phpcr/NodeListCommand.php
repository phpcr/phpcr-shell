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
use PHPCR\PropertyType;
use PHPCR\ItemNotFoundException;
use PHPCR\PropertyInterface;
use PHPCR\NodeInterface;

class NodeListCommand extends BasePhpcrCommand
{
    protected $formatter;
    protected $textHelper;
    protected $maxLevel;
    protected $time;
    protected $nbNodes;

    protected function configure()
    {
        $this->setName('node:list');
        $this->setDescription('List the children / properties of this node at the given path or with the given UUID');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Path of node', '.');
        $this->addOption('children', null, InputOption::VALUE_NONE, 'List only the children of this node');
        $this->addOption('properties', null, InputOption::VALUE_NONE, 'List only the properties of this node');
        $this->addOption('level', 'L', InputOption::VALUE_REQUIRED, 'Depth of tree to show');
        $this->addOption('template', 't', InputOption::VALUE_NONE, 'Show template nodes and properties');
        $this->setHelp(<<<HERE
List both or one of the children and properties of this node.

Multiple levels can be shown by using the <info>--level</info> option.

The <info>node:list</info> command can also shows template nodes and properties as defined a nodes node-type by
using the <info>--template</info> option. Template nodes and properties are prefixed with the "@" symbol.

The command accepts either a path (relative or absolute) to the node, a UUID or a pattern:

    PHPCRSH> node:list 842e61c0-09ab-42a9-87c0-308ccc90e6f4
    PHPCRSH> node:list /tests/foobar
    PHPCRSH> node:list /tests/*/foobar
    PHPCRSH> node:list /tests/*/foo*
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatter = $this->get('helper.result_formatter');
        $this->textHelper = $this->get('helper.text');
        $this->maxLevel = $input->getOption('level');

        $this->showChildren = $input->getOption('children');
        $this->showProperties = $input->getOption('properties');
        $this->showTemplate = $input->getOption('template');
        $this->time = 0;
        $this->nbNodes = 0;

        $config = $this->get('config.config.phpcrsh');

        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');

        try {
            $nodes = array($session->getNodeByPathOrIdentifier($path));
            $filter = null;
        } catch (\Exception $e) {
            $parentPath = $this->get('helper.path')->getParentPath($path);

            $filter = substr($path, strlen($parentPath));

            if ($filter[0] == '/') {
                $filter = substr($filter, 1);
            }

            $start = microtime(true);
            $nodes = $session->findNodes($parentPath);
            $this->time = microtime(true) - $start;
        }

        if (!$this->showChildren && !$this->showProperties) {
            $this->showChildren = true;
            $this->showProperties = true;
        }

        foreach ($nodes as $node) {
            $table = $this->get('helper.table')->create();
            $this->renderNode($node, $table, array(), $filter);

            if ($table->getNumberOfRows() > 0) {
                $output->writeln(sprintf('<path>%s</path> [%s] > %s',
                    $node->getPath(),
                    $node->getPrimaryNodeType()->getName(),
                    implode(', ', $node->getPrimaryNodeType()->getDeclaredSupertypeNames())
                ));
                $table->render($output);
            }
        }

        if ($config['show_execution_time_list']) {
            $output->writeln(sprintf(
                '%s nodes in set (%s sec)',
                $this->nbNodes,
                number_format($this->time, $config['execution_time_expansion']))
            );
        }
    }

    private function renderNode($currentNode, $table, $spacers = array(), $filter = null)
    {
        $this->nbNodes++;
        if ($this->showChildren) {
            $this->renderChildren($currentNode, $table, $spacers, $filter);
        }

        if ($this->showProperties) {
            $this->renderProperties($currentNode, $table, $spacers, $filter);
        }
    }

    private function renderChildren($currentNode, $table, $spacers, $filter = null)
    {
        $start = microtime(true);
        $children = $currentNode->getNodes($filter ?: null);
        $this->time += microtime(true) - $start;

        $nodeType = $currentNode->getPrimaryNodeType();
        $childNodeDefinitions = $nodeType->getDeclaredChildNodeDefinitions();
        $childNodeNames = array();
        foreach ($childNodeDefinitions as $childNodeDefinition) {
            $childNodeNames[$childNodeDefinition->getName()] = $childNodeDefinition;
        }

        $i = 0;
        foreach ($children as $child) {
            $i++;
            if (isset($childNodeNames[$child->getName()])) {
                unset($childNodeNames[$child->getName()]);
            }

            $primaryItemValue = '';
            try {
                $primaryItem = $child->getPrimaryItem();

                if ($primaryItem instanceof PropertyInterface) {
                    $primaryItemValue = $this->textHelper->truncate($this->formatter->formatValue($primaryItem), 55);
                } elseif ($primaryItem instanceof NodeInterface) {
                    $primaryItemValue = sprintf('+%s', $primaryItem->getName());
                }
            } catch (ItemNotFoundException $e) {
            }

            $isLast = count($children) === $i;

            $table->addRow(array(
                '<node>' . implode('', $spacers) . $this->formatter->formatNodeName($child) . '</node>',
                $child->getPrimaryNodeType()->getName(),
                $primaryItemValue,
            ));

            if (count($spacers) < $this->maxLevel) {
                $newSpacers = $spacers;
                if ($isLast) {
                    $newSpacers[] = '  ';
                } else {
                    $newSpacers[] = '| ';
                }

                $this->renderNode($child, $table, $newSpacers);
            }
        }

        if ($this->showTemplate) {
            // render empty schematic children
            foreach ($childNodeNames as $childNodeName => $childNodeDefinition) {
                // @todo: Determine and show cardinality, 1..*, *..*, 0..1, etc.
                $table->addRow(array(
                    '<templatenode>' . implode('', $spacers) . '@' . $childNodeName . '</templatenode>',
                    implode('|', $childNodeDefinition->getRequiredPrimaryTypeNames()),
                    '',
                ));
            }
        }
    }

    private function renderProperties($currentNode, $table, $spacers, $filter = null)
    {
        $properties = $currentNode->getProperties($filter ?: null);

        try {
            $primaryItem = $currentNode->getPrimaryItem();
        } catch (ItemNotFoundException $e) {
            $primaryItem = null;
        }

        $nodeType = $currentNode->getPrimaryNodeType();
        $propertyDefinitions = $nodeType->getDeclaredPropertyDefinitions();

        $propertyNames = array();
        foreach ($propertyDefinitions as $name => $propertyDefinition) {
            $propertyNames[$propertyDefinition->getName()] = $propertyDefinition;
        }

        $i = 0;
        foreach ($properties as $name => $property) {
            try {
                $i++;
                if (isset($propertyNames[$name])) {
                    unset($propertyNames[$name]);
                }

                $valueCell = $this->formatter->formatValue($property);
            } catch (\Exception $e) {
                $valueCell = '<error>' . $e->getMessage() . '</error>';
            }

            $table->addRow(array(
                '<property>' . implode('', $spacers). $name . '</property>',
                sprintf(
                    '<property-type>%s (%s)</property-type>',
                    $this->formatter->getPropertyTypeName($property->getType()),
                    implode(',', (array) $property->getLength()) ? : '0'
                ),
                $valueCell,
            ));
        }

        if ($this->showTemplate) {
            foreach ($propertyNames as $propertyName => $property) {
                $table->addRow(array(
                    '<templateproperty>' . implode('', $spacers). '@' . $propertyName . '</templateproperty>',
                    '<property-type>' . strtoupper(PropertyType::nameFromValue($property->getRequiredType())) . '</property-type>',
                    '',
                ));
            }
        }
    }
}
