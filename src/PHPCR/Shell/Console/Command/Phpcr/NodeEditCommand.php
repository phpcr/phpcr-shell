<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\ImportUUIDBehaviorInterface;
use PHPCR\Shell\Console\Helper\PathHelper;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use PHPCR\Shell\Serializer\NodeNormalizer;
use Symfony\Component\Serializer\Serializer;
use PHPCR\Shell\Serializer\YamlEncoder;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\PathNotFoundException;

class NodeEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:edit');
        $this->setDescription('Edit the given node in the EDITOR configured by the system');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Optional type to use when creating new nodes', 'nt:unstructured');
        $this->setHelp(<<<HERE
Edit the given node
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $parentPath = $this->getHelper('path')->getParentPath($path);
        $nodeName = $this->getHelper('path')->getNodeName($path);
        $type = $input->getOption('type');

        $editor = $this->getHelper('editor');
        $dialog = $this->getHelper('dialog');

        try {
            $node = $session->getNode($path);
        } catch (PathNotFoundException $e) {
            $parentNode = $session->getNode($parentPath);
            $node = $parentNode->addNode($nodeName, $type);
        }


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
                    'node' => $node
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
