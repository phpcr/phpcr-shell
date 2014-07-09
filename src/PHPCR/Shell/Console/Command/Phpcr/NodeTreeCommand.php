<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\NodeInterface;

class NodeTreeCommand extends NodeListCommand
{
    protected $textHelper;
    protected $session;

    protected $showChildren;
    protected $showProperties;

    protected function configure()
    {
        parent::configure();
        $this->setName('node:tree');
        $this->setDescription('Display the current node as a tree');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Path of node', '.');
        $this->addOption('filter', 'f', InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Optional filter to apply');
        $this->addOption('level', 'L', InputOption::VALUE_REQUIRED, 'Depth of tree to show');
        $this->setHelp(<<<HERE
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatter = $this->getHelper('result_formatter');
        $this->textHelper = $this->getHelper('text');
        $this->session = $this->getHelper('phpcr')->getSession();

        $this->filters = $input->getOption('filter');
        $this->level = $input->getOption('level');

        $this->showChildren = $input->getOption('children');
        $this->showProperties = $input->getOption('properties');

        $path = $input->getArgument('path');
        $currentNode = $session->getNodeByPathOrIdentifier($path);

        if (!$showChildren && !$showProperties) {
            $this->showChildren = true;
            $this->showProperties = true;
        }

        $output->writeln('<node>.</node>');
        $this->renderNode($currentNode, 1);
    }

    protected function renderNode(NodeInterface $node, OutputInterface $output)
    {
        foreach ($node->getNodes($this->filters ? : null) as $child) {
            $output->writeln($this->formatNode($child));
        }
    }

    protected function formatNode(NodeInterface $node)
    {
        return '<node>' . $this->formatter->formatNodeName($child) . '</node>';
    }
}
