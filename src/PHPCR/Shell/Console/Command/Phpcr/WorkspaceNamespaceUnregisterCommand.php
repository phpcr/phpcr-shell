<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class WorkspaceNamespaceUnregisterCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('workspace:namespace:unregister');
        $this->setDescription('Unregister a namespace');
        $this->addArgument('uri', InputArgument::REQUIRED, 'The URI to be removed');
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
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $uri = $input->getArgument('uri');

        $namespaceRegistry->unregisterNamespaceByURI($uri);
    }
}
