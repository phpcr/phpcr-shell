<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\RepositoryInterface;

class RetentionPolicyGetCommand extends PhpcrShellCommand
{
    protected function configure()
    {
        $this->setName('retention:policy:get');
        $this->setDescription('Get a retention policy for specified node UNSUPPORTED');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
Gets the retention policy of a node identified by its path.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_RETENTION_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $retentionManager = $session->getRetentionManager();
        $absPath = $input->getArgument('absPath');

        $policy = $retentionManager->getRetentionPolicy($absPath);
        if (!$policy) {
            $output->writeln('No retention policy');
        } else {
            $output->writeln($policy->getName());
        }
    }
}
