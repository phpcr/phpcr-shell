<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\PropertyType;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\PathNotFoundException;

class NodeFileImportCommand extends BasePhpcrCommand
{
    /**
     * @var PHPCR\SessionInterface
     */
    protected $session;

    protected function configure()
    {
        $this->setName('file:import');
        $this->setDescription('Import a file at the given path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to import file to');
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to file to import');
        $this->addOption('mime-type', null, InputOption::VALUE_REQUIRED, 'Mime type (optional, auto-detected)');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Force overwriting any existing node');
        $this->addOption('no-container', null, InputOption::VALUE_NONE, 'Do not wrap in a JCR nt:file, but write directly to the specified property');
        $this->setHelp(<<<HERE
Import an external file into the repository.

The file will be imported as a node of built-in type <comment>nt:file</comment>.

If a Node is specified as <info>path</info> then the filename of the imported file will be used
as the new node, otherwise, if the target <info>path</info> does not exist, then it is assumed
that the path is the target path for the new file, including the filename.

    PHPCRSH> file:import ./ foobar.png
    PHPCRSH> file:import ./barfoo.png foobar.png

In the first example above will create <info>/foobar.png</info>, whereas the second will create
<info>./barfoo.png</info>.

By default the file will be imported in a container, i.e. a node with type <info>nt:file</info>. In
addition to the file data, the node will contain metadata.

Alternatively you can specify the <info>--no-container</info> option to import directly to a single property.

The mime-type of the file (in the case where a container is used) will be automatically determined unless
specified with <info>--mime-type</info>.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->session = $this->get('phpcr.session');

        $filePath = $input->getArgument('file');
        $force = $input->getOption('force');
        $noContainer = $input->getOption('no-container');

        $path = $this->session->getAbsPath($input->getArgument('path'));
        $mimeType = $input->getOption('mime-type');
        $filename = basename($filePath);

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf(
                'File "%s" does not exist.',
                $filePath
            ));
        }

        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf(
                'File "%s" is not a regular file.',
                $filePath
            ));
        }

        try {
            // first assume the user specified the path to the parent node
            $parentNode = $this->session->getNode($path);
        } catch (PathNotFoundException $e) {
            // if the given path does not exist, assume that the basename is the target
            // filename and the dirname the path to the parent node
            $parentPath = dirname($path);
            $parentNode = $this->session->getNode($parentPath);
            $filename = basename($path);
        }

        $fhandle = fopen($filePath, 'r');

        if ($noContainer) {
            $this->importToProperty($fhandle, $filePath, $filename, $parentNode, $force);
        } else {
            $this->importToContainer($fhandle, $mimeType, $filePath, $filename, $parentNode, $force);
        }
    }

    private function importToProperty($fhandle, $filePath, $filename, $parentNode, $force)
    {
        $parentNode->setProperty($filename, $fhandle, PropertyType::BINARY);
    }

    private function importToContainer($fhandle, $mimeType, $file, $filename, $parentNode, $force)
    {
        // if no mime-type specified, guess it.
        if (!$mimeType) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file);
        }

        // handle existing node
        if ($parentNode->hasNode($filename)) {
            if (true === $force) {
                $fileNode = $parentNode->getNode($filename);
                $this->session->removeItem($fileNode->getPath());
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Node "%s" already has child "%s". Use --force to overwrite.',
                    $parentNode->getPath(),
                    $filename
                ));
            }
        }

        $fileNode = $parentNode->addNode($filename, 'nt:file');
        $contentNode = $fileNode->addNode('jcr:content', 'nt:unstructured');
        $contentNode->setProperty('jcr:data', $fhandle, PropertyType::BINARY);
        $contentNode->setProperty('jcr:mimeType', $mimeType);
    }
}
