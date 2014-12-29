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

class ConfigReloadCommand extends BaseCommand
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
        $config = $this->get('config.manager');
        $config->loadConfig();
    }
}
