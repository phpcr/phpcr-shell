<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;
use PHPCR\PathNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class SessionPropertyEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('session:property:edit');
        $this->setDescription('Edit the property at the given absolute path using EDITOR');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to property');
        $this->setHelp(<<<HERE
Launches the editor as identified by the EDITOR environment variable.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('result_formatter');
        $session = $this->getHelper('phpcr')->getSession();
        $absPath = $input->getArgument('absPath');

        $fs = new Filesystem();
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpcr-shell';
        if (!file_exists($dir)) {
            $fs->mkdir($dir);
        }

        $absPath = $input->getArgument('absPath');
        $property = $session->getProperty($absPath);

        $tmpName = tempnam($dir, '');
        file_put_contents($tmpName, $formatter->formatValue($property, true));
        $editor = getenv('EDITOR');

        if (!$editor) {
            throw new \Exception('No EDITOR environment variable set.');
        }

        system($editor . ' ' . $tmpName . ' > `tty`');

        $contents = file_get_contents($tmpName);
        $fs->remove($tmpName);

        $property->setValue($contents);
        $session->save();
    }
}

