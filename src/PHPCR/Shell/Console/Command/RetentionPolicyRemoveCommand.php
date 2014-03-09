<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RetentionPolicyRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('retention:policy:remove');
        $this->setDescription('Remove a retention policy for specified node UNSUPPORTED');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
Removes the retention policy of a node identified by its path.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $retentionManager = $session->getRetentionManager();
        $absPath = $input->removeArgument('absPath');

        $policy = $retentionManager->getRetentionPolicy($absPath);
        if (!$policy) {
            $output->writeln('No retention policy');
        } else {
            $output->writeln($policy->remove());
        }
    }
}
