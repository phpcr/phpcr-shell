<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\NodeInterface;
use PHPCR\Util\NodeHelper;

class ListTreeCommand extends Command
{
    protected $maxDepth;
    protected $maxResults;
    protected $showSystem;

    protected $nodeCount = 0;

    public function configure()
    {
        $this->setName('ls');
        $this->setDescription('List tree');
        $this->addOption('max-depth', 'd', InputOption::VALUE_REQUIRED, 'Depth', 1);
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit results', null);
        $this->addOption('show-system', null, InputOption::VALUE_NONE, 'Show system nodes');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->nodeCount = 0;

        $session = $this->getHelper('phpcr')->getSession();
        $node = $session->getNode($session->getCwd());
        $this->maxDepth = $input->getOption('max-depth');
        $this->maxResults = $input->getOption('limit');
        $this->showSystem = $input->getOption('show-system');

        $rows = new \ArrayObject();
        $this->iterateTree($node, $rows);

        $table = clone $this->getHelper('table');
        $table->setHeaders(array('Node / Prop', 'Type', 'Value'));

        foreach ($rows as $row) {
            $table->addRow($row);
        }

        $table->render($output);

        $output->writeln('');
        $output->writeln($this->nodeCount . ' node(s)');
    }

    public function iterateTree(NodeInterface $node, $rows, $depth = -1)
    {
        if (null !== $this->maxResults && $this->nodeCount >= $this->maxResults) {
            return;
        }

        if (true === NodeHelper::isSystemItem($node) && false === $this->showSystem) {
            return;
        }

        $this->nodeCount++;

        $formatter = $this->getHelper('result_formatter');
        $properties = $node->getProperties();

        $rows[] = array(
            str_repeat(' ', $depth + 1) . '<info>' . $node->getName() . '/</info>', 
            '<info>' . $node->getPrimaryNodeType()->getName() . '</info>',
            '',
        );

        $depth++;
        if ($depth >= $this->maxDepth) {
            return;
        }

        foreach ($properties as $key => $property) {
            if (true === NodeHelper::isSystemItem($property) && false === $this->showSystem) {
                continue;
            }
            $rows[] = array(
                sprintf('%s -<comment>%s</comment>', str_repeat(' ', $depth), $key),
                $formatter->getPropertyTypeName($property->getType()) . ($property->isMultiple() ? '[]' : ''),
                substr($formatter->formatValue($property), 0, 55),
            );
        }

        foreach ($node->getNodes() as $child) {
            if ($depth < $this->maxDepth) {
                $this->iterateTree($child, $rows, $depth);
            }
        }
    }
}

