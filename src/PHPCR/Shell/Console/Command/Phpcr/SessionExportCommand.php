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
        $this->setDescription('Export the to XML');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Path of node to export');
        $this->addArgument('file', InputArgument::REQUIRED, 'File to export to');
        $this->addOption('no-recurse', null, InputOption::VALUE_NONE, 'Do not recurse');
        $this->addOption('skip-binary', null, InputOption::VALUE_NONE, 'Skip binary properties');
        $this->addOption('document', null, InputOption::VALUE_NONE, 'Export the document view');
        $this->addOption('pretty', null, InputOption::VALUE_NONE, 'Export in human readable format');
        $this->setHelp(<<<HERE
Serializes the node (and if <info>--no-recurse</info> is false, the whole subgraph) at
<info>absPath</info> as an XML stream and outputs it to the supplied URI. The
resulting XML is in the system view form. Note that <info>absPath</info> must be
the path of a node, not a property.

If <info>--skip-binary</info> is true then any properties of PropertyType::BINARY will
be serialized as if they are empty. That is, the existence of the
property will be serialized, but its content will not appear in the
serialized output (the <sv:value> element will have no content). Note
that in the case of multi-value BINARY properties, the number of values
in the property will be reflected in the serialized output, though they
will all be empty. If <info>--skip-binary</info> is false then the actual value(s) of
each BINARY property is recorded using Base64 encoding.

If <info>no-recurse</info> is true then only the node at <info>abs-path</info> and its properties,
but not its child nodes, are serialized. If <info>no-recurse</info> is false then the
entire subgraph rooted at <info>absPath</info> is serialized.

If the user lacks read access to some subsection of the specified tree,
that section simply does not get serialized, since, from the user's
point of view, it is not there.

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
