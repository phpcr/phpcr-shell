<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use PHPCR\Shell\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SessionNamespaceListCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('session:namespace:list');
        $this->setDescription('List all namespace prefix to URI  mappings in current session');
        $this->setHelp(<<<'HERE'
List all namespace prefix to URI  mappings in current session
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $prefixes = $session->getNamespacePrefixes();

        $table = new Table($output);
        $table->setHeaders(['Prefix', 'URI']);

        foreach ($prefixes as $prefix) {
            $uri = $session->getNamespaceURI($prefix);
            $table->addRow([$prefix, $uri]);
        }

        $table->render($output);

        return 0;
    }
}
