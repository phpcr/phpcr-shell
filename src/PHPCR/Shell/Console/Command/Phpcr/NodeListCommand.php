<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class NodeListCommand extends Command
{
    protected $formatter;
    protected $filters;
    protected $textHelper;
    protected $maxLevel;

    protected function configure()
    {
        $this->setName('node:list');
        $this->setDescription('List the children / properties of this node');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Path of node', '.');
        $this->addOption('children', null, InputOption::VALUE_NONE, 'List only the children of this node');
        $this->addOption('properties', null, InputOption::VALUE_NONE, 'List only the properties of this node');
        $this->addOption('filter', 'f', InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Optional filter to apply');
        $this->addOption('level', 'L', InputOption::VALUE_REQUIRED, 'Depth of tree to show');
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
        $this->maxLevel = $input->getOption('level');

        $this->showChildren = $input->getOption('children');
        $this->showProperties = $input->getOption('properties');

        $session = $this->getHelper('phpcr')->getSession();

        $path = $session->getAbsPath($input->getArgument('path'));
        $currentNode = $session->getNode($path);

        if (!$this->showChildren && !$this->showProperties) {
            $this->showChildren = true;
            $this->showProperties = true;
        }

        $table = clone $this->getHelper('table');

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

        $i = 0;
        foreach ($children as $child) {
            $i++;
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
    }

    private function renderProperties($currentNode, $table, $spacers)
    {
        $properties = $currentNode->getProperties($this->filters ? : null);

        $i = 0;
        foreach ($properties as $name => $property) {
            $i++;
            $table->addRow(array(
                '<property>' . implode('', $spacers). $name . '</property>',
                '<property-type>' . $this->formatter->getPropertyTypeName($property->getType()) . '</property-type>',
                $this->textHelper->truncate($this->formatter->formatValue($property), 55),
            ));
        }
    }
}
