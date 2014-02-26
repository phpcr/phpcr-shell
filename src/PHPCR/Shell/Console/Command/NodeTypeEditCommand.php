<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;

class NodeTypeEditCommand extends Command
{
    protected function configure()
    {
        $this->setName('node-type:edit');
        $this->setDescription('Edit the current node');
        $this->addArgument('nodeTypeName', null, InputArgument::REQUIRED, 'The name of the node type to edit');
        $this->setHelp(<<<HERE
Edit the given node type name with the editor defined in the EDITOR environment variable.
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

        try {
            $nodeType = $nodeTypeManager->getNodeType($nodeTypeName);
        } catch (NoSuchNodeTypeException $e) {
            throw new \Exception(sprintf(
                'The node type "%s" does not exist'
            , $nodeTypeName));
        }
        $cndWriter = new CndWriter($namespaceRegistry);
        $out = $cndWriter->writeString(array($nodeType));
        $res = $editor->fromString($out);

        $valid = false;
        while (false === $valid) {
            try {
                $cndParser = new CndParser($nodeTypeManager);
                $res = $cndParser->parseString($res);

                foreach ($res['nodeTypes'] as $nodeType) {
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

                $prefix = '# ';
                $message = 'The following errors were encountered (all lines starting with ' . $prefix . ' will be ignored):';
                $message .= PHP_EOL;
                $message .= PHP_EOL;
                $message .= $e->getMessage();
                $editor->fromStringWithMessage($res, $message, $prefix);
            }
        }
    }
}
