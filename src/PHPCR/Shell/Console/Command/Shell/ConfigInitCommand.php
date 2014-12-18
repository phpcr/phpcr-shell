<?php

namespace PHPCR\Shell\Console\Command\Shell;

use PHPCR\Shell\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigInitCommand extends BaseCommand
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
        $configHelper = $this->get('config.manager');
        $configHelper->initConfig($output, $this->get('helper.question'), $input->getOption('no-interaction'));
    }
}
