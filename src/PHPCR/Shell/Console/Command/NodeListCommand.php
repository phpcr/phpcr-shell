<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;
use PHPCR\NamespaceException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\TableHelper;

class NodeListCommand extends Command
{
    protected $formatter;
    protected $filters;

    protected function configure()
    {
        $this->setName('node:list');
        $this->setDescription('List the children / properties of this node');
        $this->addOption('children', null, InputOption::VALUE_NONE, 'List only the children of this node');
        $this->addOption('properties', null, InputOption::VALUE_NONE, 'List only the properties of this node');
        $this->addOption('filter', 'f', InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Optional filter to apply');
        $this->setHelp(<<<HERE
List both or one of the children and properties of this node.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatter = $this->getHelper('result_formatter');
        $this->textHelper = $this->getHelper('text');
        $this->filters = $input->getOption('filter');

        $showChildren = $input->getOption('children');
        $showProperties = $input->getOption('properties');

        $session = $this->getHelper('phpcr')->getSession();
        $currentNode = $session->getCurrentNode();

        if (!$showChildren && !$showProperties) {
            $showChildren = true;
            $showProperties = true;
        }

        $table = clone $this->getHelper('table');

        if ($showChildren) {
            $this->renderChildren($currentNode, $table);
        }

        if ($showProperties) {
            $this->renderProperties($currentNode, $table);
        }

        $table->render($output);
    }

    private function renderChildren($currentNode, $table)
    {
        $children = $currentNode->getNodes($this->filters ? : null);

        foreach ($children as $child) {
            $table->addRow(array(
                '<node>' . $this->formatter->formatNodeName($child) . '</node>',
                $child->getPrimaryNodeType()->getName(),
                '',
            ));
        }
    }

    private function renderProperties($currentNode, $table)
    {
        $properties = $currentNode->getProperties($this->filters ? : null);

        foreach ($properties as $name => $property) {
            $table->addRow(array(
                '<property>' . $name . '</property>',
                '<property-type>' . $this->formatter->getPropertyTypeName($property->getType()) . '</property-type>',
                '<property-value>' . $this->textHelper->truncate($this->formatter->formatValue($property), 55) . '</property-value>',
            ));
        }
    }
}
