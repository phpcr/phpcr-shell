<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SessionNamespaceListCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:namespace:list');
        $this->setDescription('List all namespace prefix to URI  mappings in current session');
        $this->setHelp(<<<HERE
List all namespace prefix to URI  mappings in current session
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $prefixes = $session->getNamespacePrefixes();

        $table = clone $this->getHelper('table');
        $table->setHeaders(array('Prefix', 'URI'));

        foreach ($prefixes as $prefix) {
            $uri = $session->getNamespaceURI($prefix);
            $table->addRow(array($prefix, $uri));
        }

        $table->render($output);
    }
}
