<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\PropertyType;

class NodeListCommand extends Command
{
    protected $formatter;
    protected $filters;
    protected $textHelper;
    protected $maxLevel;

    protected function configure()
    {
        $this->setName('node:list');
        $this->setDescription('List the children / properties of this node at the given path or with the given UUID');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Path of node', '.');
        $this->addOption('children', null, InputOption::VALUE_NONE, 'List only the children of this node');
        $this->addOption('properties', null, InputOption::VALUE_NONE, 'List only the properties of this node');
        $this->addOption('filter', 'f', InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Optional filter to apply');
        $this->addOption('level', 'L', InputOption::VALUE_REQUIRED, 'Depth of tree to show');
        $this->addOption('template', 't', InputOption::VALUE_NONE, 'Show template nodes and properties');
        $this->setHelp(<<<HERE
List both or one of the children and properties of this node.

Multiple levels can be shown by using the <info>--level</info> option.

The <info>node:list</info> command can also shows template nodes and properties as defined a nodes node-type by
using the <info>--template</info> option. Template nodes and properties are prefixed with the "@" symbol.

The command accepts wither a path (relative or absolute) to the node or a UUID.

    PHPCRSH> node:list 842e61c0-09ab-42a9-87c0-308ccc90e6f4
    PHPCRSH> node:list /tests/foobar
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatter = $this->getHelper('result_formatter');
        $this->textHelper = $this->getHelper('text');
        $this->filters = $input->getOption('filter');
        $this->maxLevel = $input->getOption('level');

        $this->showChildren = $input->getOption('children');
        $this->showProperties = $input->getOption('properties');
        $this->showTemplate = $input->getOption('template');

        $session = $this->getHelper('phpcr')->getSession();
        $path = $input->getArgument('path');

        $currentNode = $session->getNodeByPathOrIdentifier($path);

        if (!$this->showChildren && !$this->showProperties) {
            $this->showChildren = true;
            $this->showProperties = true;
        }

        $table = $this->getHelper('table')->create();

        $this->renderNode($currentNode, $table);

        $table->render($output);
    }

    private function renderNode($currentNode, $table, $spacers = array())
    {
        if ($this->showChildren) {
            $this->renderChildren($currentNode, $table, $spacers);
        }

        if ($this->showProperties) {
            $this->renderProperties($currentNode, $table, $spacers);
        }
    }

    private function renderChildren($currentNode, $table, $spacers)
    {
        $children = $currentNode->getNodes($this->filters ? : null);

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

            $isLast = count($children) === $i;

            $table->addRow(array(
                '<node>' . implode('', $spacers) . $this->formatter->formatNodeName($child) . '</node>',
                $child->getPrimaryNodeType()->getName(),
                '',
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

    private function renderProperties($currentNode, $table, $spacers)
    {
        $properties = $currentNode->getProperties($this->filters ? : null);

        $nodeType = $currentNode->getPrimaryNodeType();
        $propertyDefinitions = $nodeType->getDeclaredPropertyDefinitions();

        $propertyNames = array();
        foreach ($propertyDefinitions as $name => $propertyDefinition) {
            $propertyNames[$propertyDefinition->getName()] = $propertyDefinition;
        }

        $i = 0;
        foreach ($properties as $name => $property) {
            $i++;
            if (isset($propertyNames[$name])) {
                unset($propertyNames[$name]);
            }

            $table->addRow(array(
                '<property>' . implode('', $spacers). $name . '</property>',
                '<property-type>' . $this->formatter->getPropertyTypeName($property->getType()) . '</property-type>',
                $this->textHelper->truncate($this->formatter->formatValue($property), 55),
            ));
        }

        if ($this->showTemplate) {
            foreach ($propertyNames as $propertyName => $property) {
                $table->addRow(array(
                    '<templateproperty>' . implode('', $spacers). '@' . $propertyName . '</templateproperty>',
                    '<property-type>' . strtoupper(PropertyType::nameFromValue($property->getRequiredType())) . '</property-type>',
                    ''
                ));
            }
        }
    }
}
