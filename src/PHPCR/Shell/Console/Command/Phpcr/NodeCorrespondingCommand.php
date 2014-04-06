<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;
use PHPCR\NamespaceException;
use Symfony\Component\Console\Input\InputOption;

class NodeCorrespondingCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:corresponding');
        $this->setDescription('Show the path for the current nodes corresponding path in named workspace');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('workspaceName', InputArgument::REQUIRED, 'The name of the workspace');
        $this->setHelp(<<<HERE
Returns the absolute path of the node in the specified workspace that
corresponds to this node.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $workspaceName = $input->getArgument('workspaceName');
        $currentNode = $session->getNode($path);
        $correspondingPath = $currentNode->getCorrespondingNodePath($workspaceName);
        $output->writeln($correspondingPath);
    }
}
