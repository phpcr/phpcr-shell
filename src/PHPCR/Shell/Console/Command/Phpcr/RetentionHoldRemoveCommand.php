<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\RepositoryInterface;

class RetentionHoldRemoveCommand extends PhpcrShellCommand
{
    protected function configure()
    {
        $this->setName('retention:hold:remove');
        $this->setDescription('Removes a retention hold UNSUPPORTED');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to node to which we want to remove a hold');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of hold to remove');
        $this->setHelp(<<<HERE
Removes the specified hold from the node at <info>absPath</info>.

The removal does not take effect until a save is performed.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_RETENTION_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $retentionManager = $session->getRetentionManager();
        $absPath = $input->getArgument('absPath');
        $name = $input->getArgument('name');

        $holds = $retentionManager->getHolds($absPath);
        $indexed = array();
        foreach ($holds as $hold) {
            $indexed[$hold->getName()] = $hold;
        }

        if (!isset($indexed[$name])) {
            throw new \Exception(sprintf(
                'Unknown hold "%s" for node at path "%s", it currently has: %s',
                $name, $absPath, implode(', ', array_keys($indexed))
            ));
        }

        $retentionManager->removeHold($absPath, $indexed[$name]);
    }
}
