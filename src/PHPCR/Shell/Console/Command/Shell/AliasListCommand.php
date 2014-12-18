<?php

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\Shell\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AliasListCommand extends BaseCommand
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
        $config = $this->get('config.manager');
        $aliases = $config->getConfig('alias');

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Alias', 'Command'));

        foreach ($aliases as $alias => $command) {
            $table->addRow(array($alias, $command));
        }

        $table->render($output);
    }
}
