<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\PropertyType;

class NodeFileImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:file:import');
        $this->setDescription('Import a file at the given path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to import file to');
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to file to import');
        $this->setHelp(<<<HERE
Import an external file into the repository.

The file will be imported as a node of built-in type <comment>nt:file</comment>. The new
node will be named after the file.

The mime-type will be inferred automatically..
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();

        $file = $input->getArgument('file');
        $path = $session->getAbsPath($input->getArgument('path'));

        $parentNode = $session->getNode($path);

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf(
                'File "%s" does not exist.',
                $file
            ));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);

        $filename = basename($file);
        $fileNode = $parentNode->addNode($filename, 'nt:file');
        $contentNode = $fileNode->addNode('jcr:content', 'nt:unstructured');
        $content = file_get_contents($file);
        $contentNode->setProperty('jcr:data', $content, PropertyType::BINARY);
        $contentNode->setProperty('jcr:mimeType', $mimeType);
    }
}
