<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCommand extends Command
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

