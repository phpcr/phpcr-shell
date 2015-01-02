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
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;

class SessionExportCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('session:export');
        $this->setDescription('Export the session to XML');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Path of node to export');
        $this->addArgument('file', InputArgument::REQUIRED, 'File to export to');
        $this->addOption('no-recurse', null, InputOption::VALUE_NONE, 'Do not recurse');
        $this->addOption('skip-binary', null, InputOption::VALUE_NONE, 'Skip binary properties');
        $this->addOption('document', null, InputOption::VALUE_NONE, 'Export the document view');
        $this->addOption('pretty', null, InputOption::VALUE_NONE, 'Export in human readable format');
        $this->setHelp(<<<HERE
Export the node at the given path to the named XML file.

By default the entire subgraph of the node will be exported unless the <info>--no-recurse</info> option
is given.

If <info>--skip-binary</info> is true then any properties of
PropertyType::BINARY will be serialized as if they are empty. If
<info>--skip-binary</info> is false then the actual value(s) of each BINARY
property is recorded using Base64 encoding.

The serialized output will reflect the state of the current workspace as
modified by the state of this Session. This means that pending changes
(regardless of whether they are valid according to node type
constraints) and all namespace mappings in the namespace registry, as
modified by the current session-mappings, are reflected in the output.

The output XML will be encoded in UTF-8.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $file = $input->getArgument('file');
        $pretty = $input->getOption('pretty');
        $exportDocument = $input->getOption('document');
        $dialog = $this->get('helper.question');

        if (file_exists($file)) {
            $confirmed = true;

            if (false === $input->getOption('no-interaction')) {
                $confirmed = $dialog->askConfirmation($output, 'File already exists, overwrite?');
            }

            if (false === $confirmed) {
                return;
            }
        }

        $stream = fopen($file, 'w');
        $absPath = $input->getArgument('absPath');
        PathHelper::assertValidAbsolutePath($absPath);

        if (true === $exportDocument) {
            $session->exportDocumentView(
                $absPath,
                $stream,
                $input->getOption('skip-binary'),
                $input->getOption('no-recurse')
            );
        } else {
            $session->exportSystemView(
                $absPath,
                $stream,
                $input->getOption('skip-binary'),
                $input->getOption('no-recurse')
            );
        }

        fclose($stream);

        if ($pretty) {
            $xml = new \DOMDocument(1.0);
            $xml->load($file);
            $xml->preserveWhitespace = true;
            $xml->formatOutput = true;
            $xml->save($file);
        }
    }
}
