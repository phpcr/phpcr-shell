<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;
use PHPCR\PathNotFoundException;

class SessionPropertyEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:property:edit');
        $this->setDescription('Edit the property at the given absolute path');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to property');
        $this->setHelp(<<<HERE
Edit the property at the given absolute path
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $absPath = $input->getArgument('absPath');
        exec('vim');
    }
}

