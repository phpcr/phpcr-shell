<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class WorkspaceNamespaceUnregisterCommand extends Command
{
    protected function configure()
    {
        $this->setName('workspace:namespace:unregister');
        $this->setDescription('Sets a one-to-one mapping between prefix and uri in the global namespace');
        $this->addArgument('uri', null, InputArgument::REQUIRED, 'The URI to be removed');
        $this->setHelp(<<<HERE
Removes the specified namespace URI from namespace registry.

The following restrictions apply:

- Attempting to unregister a built-in namespace (jcr, nt, mix, sv, xml or
  the empty namespace) will throw a NamespaceException.
- An attempt to unregister a namespace that is not currently registered
  will throw a NamespaceException.
- An implementation may prevent the unregistering of any other namespace
  for implementation-specific reasons by throwing a
  NamespaceException.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $uri = $input->getArgument('uri');

        $namespaceRegistry->unregisterNamespaceByURI($uri);
    }
}
