<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SessionNodeShowCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:node:show');
        $this->setDescription('Show all the properties and children of a node');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
Show a table which displays the properties and children of the node found
at the given absPath.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $absPath = $input->getArgument('absPath');

        $node = $session->getNode($absPath);
        $properties = $node->getProperties();
        $children = $node->getNodes();
        $formatter = $this->getHelper('result_formatter');
        $textHelper = $this->getHelper('text');

        $table = clone $this->getHelper('table');
        $table->setHeaders(array('Property / Node Name', 'Type / Node Type', 'Value'));

        foreach ($properties as $key => $property) {
            $table->addRow(array(
                '- ' . $key,
                $formatter->getPropertyTypeName($property->getType()) . ($property->isMultiple() ? '[]' : ''),
                $textHelper->truncate($formatter->formatValue($property), 55),
            ));
        }

        foreach ($children as $child) {
            $table->addRow(array(
                $formatter->formatNodeName($child),
                $child->getPrimaryNodeType()->getName(),
                $textHelper->truncate($formatter->formatNodePropertiesInline($child), 55)
            ));
        }

        $table->render($output);
    }
}
