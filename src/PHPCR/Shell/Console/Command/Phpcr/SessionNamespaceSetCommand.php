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

class SessionNamespaceSetCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('session:namespace:set');
        $this->setDescription('Set a namespace in the current session');
        $this->addArgument('prefix', InputArgument::REQUIRED, 'The namespace prefix to be set as identifier');
        $this->addArgument('uri', InputArgument::REQUIRED, 'The location of the namespace definition (usually a URI');
        $this->setHelp(<<<HERE
Sets the name of a namespace prefix.

Within the scope of this Session, this method maps uri to prefix. The
remapping only affects operations done through this Session. To clear
all remappings, the client must acquire a new Session.
All local mappings already present in the Session that include either
the specified prefix or the specified uri are removed and the new
mapping is added.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $prefix = $input->getArgument('prefix');
        $uri = $input->getArgument('uri');

        $session->setNamespacePrefix($prefix, $uri);
    }
}
