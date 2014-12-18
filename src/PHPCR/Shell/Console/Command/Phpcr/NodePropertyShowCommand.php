<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\PathNotFoundException;
use PHPCR\PropertyInterface;

class NodePropertyShowCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:property:show');
        $this->setDescription('Show the property at the given absolute path');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to property');
        $this->setHelp(<<<HERE
Show the property at the given absolute path
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $absPath = $session->getAbsPath($input->getArgument('absPath'));
        $resultFormatHelper = $this->get('helper.result_formatter');

        try {
            $property = $session->getItem($absPath);
        } catch (PathNotFoundException $e) {
            throw new \Exception(sprintf(
                'There is no property at the path "%s"', $absPath
            ));
        }

        if (!$property instanceof PropertyInterface) {
            throw new \Exception(sprintf(
                'Item at "%s" is not a property.',
                $absPath
            ));

        }

        $output->writeln($resultFormatHelper->formatValue($property, true));
    }
}
