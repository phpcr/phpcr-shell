<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SessionNamespaceListCommand extends BasePhpcrCommand
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
        $session = $this->get('phpcr.session');
        $prefixes = $session->getNamespacePrefixes();

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Prefix', 'URI'));

        foreach ($prefixes as $prefix) {
            $uri = $session->getNamespaceURI($prefix);
            $table->addRow(array($prefix, $uri));
        }

        $table->render($output);
    }
}
