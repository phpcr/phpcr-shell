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
use PHPCR\Shell\Serializer\NodeNormalizer;
use Symfony\Component\Serializer\Serializer;
use PHPCR\Shell\Serializer\YamlEncoder;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\PathNotFoundException;
use PHPCR\Util\UUIDHelper;

class NodeEditCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:edit');
        $this->setDescription('Edit the given node in the EDITOR configured by the system');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Optional type to use when creating new nodes', 'nt:unstructured');
        $this->setHelp(<<<HERE
Edit or create a node at the given path using the default editor as defined by the EDITOR environment variable.

When you invoke the command a temporary file in YAML format will be created on the filesystem. This file will
contain all the properties of the nodes (including system properties). You can change, add or delete properties in
the text editor and save, where upon the changes will be registered in the session and you will be returned to the
shell.

When creating a new node you can also optionally specify the type of node which should be created.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');

        if (UUIDHelper::isUUID($path)) {
            // If the node is a UUID, then just get it
            $node = $session->getNodeByIdentifier($path);
        } else {
            $path = $session->getAbsPath($path);
            // Otherwise it is a path which may or may not exist
            $parentPath = $this->get('helper.path')->getParentPath($path);
            $nodeName = $this->get('helper.path')->getNodeName($path);
            $type = $input->getOption('type');

            try {
                // if it exists, then great
                $node = $session->getNodeByPathOrIdentifier($path);
            } catch (PathNotFoundException $e) {
                // if it doesn't exist then we create it
                $parentNode = $session->getNode($parentPath);
                $node = $parentNode->addNode($nodeName, $type);
            }
        }

        $editor = $this->get('helper.editor');
        $dialog = $this->get('helper.question');

        $skipBinary = true;
        $noRecurse = true;

        // for now we only support YAML
        $encoders = array(new YamlEncoder());
        $nodeNormalizer = new NodeNormalizer();
        $serializer = new Serializer(array($nodeNormalizer), $encoders);
        $outStr = $serializer->serialize($node, 'yaml');

        $tryAgain = false;
        $message = '';
        $error = '';
        $notes = implode("\n", $nodeNormalizer->getNotes());

        do {
            $message = '';
            if ($error) {
                $template = <<<EOT
Error encounred:
%s

EOT
                ;
                $message .= sprintf($template, $error);
            }

            if ($notes) {
                $template = <<<EOT
NOTE:
%s

EOT
                ;
                $message .= sprintf($template, $notes);
            }

            // string pass to editor
            if ($message) {
                $inStr = $editor->fromStringWithMessage($outStr, $message, '# ', 'yml');
            } else {
                $inStr = $editor->fromString($outStr, 'yml');
            }

            try {
                $norm = $serializer->deserialize($inStr, 'PHPCR\NodeInterface', 'yaml', array(
                    'node' => $node,
                ));
                $tryAgain = false;
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $output->writeln('<error>' . $error . '</error>');
                if (false === $input->getOption('no-interaction')) {
                    $tryAgain = $dialog->askConfirmation($output, 'Do you want to try again? (y/n)');
                }
                $outStr = $inStr;
            }
        } while ($tryAgain == true);

        if ($error) {
            return 1;
        }

        return 0;
    }
}
