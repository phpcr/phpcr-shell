<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class VersionRestoreCommand extends Command
{
    protected function configure()
    {
        $this->setName('version:restore');
        $this->setDescription('Restore a node version');
        $this->setHelp(<<<HERE
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception('TODO');
    }
}
