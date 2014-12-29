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

class ClearCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('shell:clear');
        $this->setDescription('Clear the screen');
        $this->setHelp(<<<EOT
Clear the screen
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write("\033[2J\033[;H");
    }
}
