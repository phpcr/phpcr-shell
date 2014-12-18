<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WorkspaceNamespaceListCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('workspace:namespace:list');
        $this->setDescription('List all namespace prefix to URI  mappings in current workspace');
        $this->setHelp(<<<HERE
List all namespace prefix to URI mappings in current workspace
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $prefixes = $namespaceRegistry->getPrefixes();

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Prefix', 'URI'));

        foreach ($prefixes as $prefix) {
            $uri = $namespaceRegistry->getURI($prefix);
            $table->addRow(array($prefix, $uri));
        }

        $table->render($output);
    }
}
