<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AliasListCommand extends Command
{
    public function configure()
    {
        $this->setName('shell:alias:list');
        $this->setDescription('List all the registered aliases');
        $this->setHelp(<<<EOT
List the aliases as defined in <info>~/.phpcrsh/aliases.yml</info>.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getHelper('config');
        $aliases = $config->getConfig('alias');

        $table = clone $this->getHelper('table');
        $table->setHeaders(array('Alias', 'Command'));

        foreach ($aliases as $alias => $command) {
            $table->addRow(array($alias, $command));
        }

        $table->render($output);
    }
}
