<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigInitCommand extends Command
{
    protected $output;

    public function configure()
    {
        $this->setName('shell:config:init');
        $this->setDescription('Initialize a local configuration with default values');
        $this->setHelp(<<<EOT
Initialize a new configuration folder, <info>.phpcrsh</info> in the users HOME directory.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $configHelper = $this->getHelper('config');
        $configHelper->initConfig($output, $this->getHelper('dialog'));
    }
}
