<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigReloadCommand extends Command
{
    protected $output;

    public function configure()
    {
        $this->setName('shell:config:reload');
        $this->setDescription('Reload the configuration');
        $this->setHelp(<<<EOT
Reload the configuration
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $config = $this->getHelper('config');
        $config->loadConfig();
    }
}
