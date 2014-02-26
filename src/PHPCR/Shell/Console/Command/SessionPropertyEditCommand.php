<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use PHPCR\Util\ValueConverter;

class SessionPropertyEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:property:edit');
        $this->setDescription('Edit the property at the given absolute path using EDITOR');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to property');
        $this->addArgument('multivalue-index', InputArgument::OPTIONAL, 'If editing a multivalue property, the index of the value to edit.');
        $this->setHelp(<<<HERE
Launches the editor as identified by the EDITOR environment variable.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('result_formatter');
        $session = $this->getHelper('phpcr')->getSession();
        $editor = $this->getHelper('editor');

        $absPath = $input->getArgument('absPath');
        $multivalueIndex = $input->getArgument('multivalue-index');

        $valueConverter = new ValueConverter();

        $absPath = $input->getArgument('absPath');
        $property = $session->getProperty($absPath);

        if ($property->isMultiple()) {
            if (null === $multivalueIndex) {
                throw new \InvalidArgumentException(
                    'You specified a multivalue property but did not provide an index'
                );
            }

            $v = $property->getValue();

            if (!isset($v[$multivalueIndex])) {
                throw new \OutOfBoundsException(sprintf(
                    'The multivalue index you specified ("%s") does not exist', $multivalueIndex
                ));
            }

            $originalValue = $v;
            $value = $v[$multivalueIndex];
        } else {
            $value = $formatter->formatValue($property, true);
        }

        $contents = $editor->fromString($value);

        $value = $valueConverter->convertType($contents, $property->getType());

        if ($property->isMultiple()) {
            $originalValue[$multivalueIndex] = $value;
            $value = $originalValue;
        }

        $property->setValue($value);
    }
}
