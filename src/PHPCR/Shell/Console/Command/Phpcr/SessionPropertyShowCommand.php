<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\PathNotFoundException;

class SessionPropertyShowCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:property:show');
        $this->setDescription('Show the property at the given absolute path');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to property');
        $this->setHelp(<<<HERE
Show the property at the given absolute path
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $absPath = $input->getArgument('absPath');
        $resultFormatHelper = $this->getHelper('result_formatter');

        try {
            $property = $session->getItem($absPath);
        } catch (PathNotFoundException $e) {
            throw new \Exception(sprintf(
                'There is no property at the path "%s"', $absPath
            ));
        }

        $output->writeln($resultFormatHelper->formatValue($property, true));
    }
}
