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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;

class NodeTypeEditCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node-type:edit');
        $this->setDescription('Edit or create a node type');
        $this->addArgument('nodeTypeName', InputArgument::REQUIRED, 'The name of the node type to edit or create');
        $this->setHelp(<<<HERE
Edit the given node type name with the editor defined in the EDITOR environment variable.

If the node type does not exist, it will be created. All node types must be prefixed with
a namespace prefix as shown in the <info>session:namespace:list</info> command

    PHPCRSH> node-type:edit nt:examplenode

Will open an editor with a new node type.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $editor = $this->get('helper.editor');
        $dialog = $this->get('helper.question');
        $nodeTypeName = $input->getArgument('nodeTypeName');
        $workspace = $session->getWorkspace();
        $namespaceRegistry = $workspace->getNamespaceRegistry();
        $nodeTypeManager = $workspace->getNodeTypeManager();

        try {
            $nodeType = $nodeTypeManager->getNodeType($nodeTypeName);
            $cndWriter = new CndWriter($namespaceRegistry);
            $out = $cndWriter->writeString(array($nodeType));
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

            // so we will create one ..
            $out = <<<EOT
<$namespace ='$uri'>
[$namespace:$name] > nt:unstructured

EOT
            ;
            $message = <<<EOT
Creating a new node type: $nodeTypeName
EOT
            ;
        }

        $valid = false;
        $prefix = '# ';
        do {
            $res = $editor->fromStringWithMessage($out, $message);

            if (empty($res)) {
                $output->writeln('<info>Editor emptied the CND file, doing nothing. Use node-type:delete to remove node types.</info>');

                return 0;
            }

            try {
                $cndParser = new CndParser($nodeTypeManager);
                $namespacesAndNodeTypes = $cndParser->parseString($res);

                foreach ($namespacesAndNodeTypes['nodeTypes'] as $nodeType) {
                    $nodeTypeManager->registerNodeType($nodeType, true);
                }
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
