<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $configHelper->initConfig($output, $input->getOption('no-interaction'));
    }
}
