<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\NodeType\Serializer\YAMLSerializer;
use PHPCR\Util\NodeType\Serializer\YAMLDeserializer;

class NodeTypeEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('node-type:edit');
        $this->setDescription('Edit or create a node type');
        $this->addArgument('nodeTypeName', null, InputArgument::REQUIRED, 'The name of the node type to edit or create');
        $this->setHelp(<<<HERE
Edit the given node type name with the editor defined in the EDITOR environment variable.

If the node type does not exist, it will be created. All node types must be prefixed with
a namespace prefix as shown in the <info>session:namespace:list</info> command

    $ node-type:edit nt:examplenode

Will open an editor with a new node type.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $editor = $this->getHelper('editor');
        $dialog = $this->getHelper('dialog');
        $nodeTypeName = $input->getArgument('nodeTypeName');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();

        $serializer = new YAMLSerializer();

        try {
            $nodeType = $nodeTypeManager->getNodeType($nodeTypeName);
            $message = null;
        } catch (NoSuchNodeTypeException $e) {
            $parts = explode(':', $nodeTypeName);

            if (count($parts) != 2) {
                throw new \InvalidArgumentException(
                    'Node type names must be prefixed with a namespace, e.g. ns:foobar'
                );
            }
            list($namespace, $name)  = $parts;
            $uri = $session->getNamespaceURI($namespace);
            $nodeType = $nodeTypeManager->createNodeTypeTemplate();
            $propertyTemplate = $nodeTypeManager->createPropertyDefinitionTemplate();
            $propertyTemplate->setName($namespace . ':change-me');
            $childTemplate = $nodeTypeManager->createNodeDefinitionTemplate();
            $childTemplate->setName($namespace . ':change-me');

            $nodeType->setName($nodeTypeName);
            $nodeType->getNodeDefinitionTemplates()->append($childTemplate);
            $nodeType->getPropertyDefinitionTemplates()->append($propertyTemplate);

            // so we will create one ..
            $message = <<<EOT
Creating a new node type: $nodeTypeName

An example property and an example node have been added. Feel free to delete or change them.
EOT
            ;
        }

        $out = $serializer->serialize($nodeType);

        $valid = false;
        $prefix = '# ';
        do {
            $res = $editor->fromStringWithMessage($out, $message, $prefix, '.yml');

            if (empty($res)) {
                $output->writeln('<info>Editor emptied the CND file, doing nothing. Use node-type:delete to remove node types.</info>');

                return 0;
            }

            try {
                $serializer = new YAMLDeserializer($session);
                $nodeType = $serializer->deserialize($res);
                $nodeTypeManager->registerNodeType($nodeType, true);
                $valid = true;
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');

                $tryAgain = false;
                if (false === $input->getOption('no-interaction')) {
                    $tryAgain = $dialog->askConfirmation($output, 'Do you want to try again? (y/n)');
                }

                if (false === $tryAgain) {
                    return 1;
                }

                $message = 'The following errors were encountered (all lines starting with ' . $prefix . ' will be ignored):';
                $message .= PHP_EOL;
                $message .= PHP_EOL;
                $message .= $e->getMessage();
                $out = $res;
            }
        } while (false === $valid);
    }
}
