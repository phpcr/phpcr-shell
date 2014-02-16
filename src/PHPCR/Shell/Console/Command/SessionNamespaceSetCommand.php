<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;

class SessionNamespaceSetCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:namespace:set');
        $this->setDescription('Set a namespace in the current session');
        $this->addArgument('prefix', null, InputArgument::REQUIRED, 'The namespace prefix to be set as identifier');
        $this->addArgument('uri', null, InputArgument::REQUIRED, 'The location of the namespace definition (usually a URI');
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
        $session = $this->getHelper('phpcr')->getSession();
        $prefix = $input->getArgument('prefix');
        $uri = $input->getArgument('uri');

        $session->setNamespacePrefix($prefix, $uri);
        $session->save();
    }
}


