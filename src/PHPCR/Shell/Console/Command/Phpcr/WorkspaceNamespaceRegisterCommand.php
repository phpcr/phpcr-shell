<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class WorkspaceNamespaceRegisterCommand extends Command
{
    protected function configure()
    {
        $this->setName('workspace:namespace:register');
        $this->setDescription('Sets a one-to-one mapping between prefix and uri in the global namespace');
        $this->addArgument('prefix', InputArgument::REQUIRED, 'The namespace prefix to be mapped');
        $this->addArgument('uri', InputArgument::REQUIRED, 'The URI to be mapped');
        $this->setHelp(<<<HERE
List all namespace prefix to URI  mappings in current session
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $prefix = $input->getArgument('prefix');
        $uri = $input->getArgument('uri');

        $namespaceRegistry->registerNamespace($prefix, $uri);
    }
}
