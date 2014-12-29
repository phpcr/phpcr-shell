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

class WorkspaceNamespaceRegisterCommand extends BasePhpcrCommand
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
        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();

        $prefix = $input->getArgument('prefix');
        $uri = $input->getArgument('uri');

        $namespaceRegistry->registerNamespace($prefix, $uri);
    }
}
