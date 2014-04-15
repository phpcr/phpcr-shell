<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\CND\Writer\CndWriter;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\Util\CND\Parser\CndParser;
use PHPCR\NamespaceException;

class NodeReferencesCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:references');
        $this->setDescription('Returns all REFERENCE properties that refer to this node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node');
        $this->addArgument('name', InputArgument::OPTIONAL, 'Limit references to given name');
        $this->setHelp(<<<HERE
This command returns all REFERENCE properties that refer to this node,
have the specified name and that are accessible through the current
Session.

If the <info>name</info> parameter is null then all referring REFERENCES are returned
regardless of name.

Some implementations may only return properties that have been
persisted. Some may return both properties that have been persisted and
those that have been dispatched but not persisted (for example, those
saved within a transaction but not yet committed) while others
implementations may return these two categories of property as well as
properties that are still pending and not yet dispatched.

In implementations that support versioning, this method does not return
properties that are part of the frozen state of a version in version
storage.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $path = $session->getAbsPath($input->getArgument('path'));
        $currentNode = $session->getNode($path);
        $name = $input->getArgument('name');

        $references = array(
            'weak' => array(),
            'strong' => array(),
        );

        $references['weak'] = $currentNode->getWeakReferences($name ? : null);
        $references['strong'] = $currentNode->getReferences($name ? : null);

        $table = clone $this->getHelper('table');
        $table->setHeaders(array(
            'Type', 'Property', 'Node Path'
        ));

        foreach ($references as $type => $typeReferences) {
            foreach ($typeReferences as $property) {
                if ($property->isMultiple()) {
                    $nodes = $property->getNode();
                } else {
                    $nodes = array($property->getNode());
                }
                
                $nodePaths = array();

                foreach ($nodes as $node) {
                    $nodePaths[] = $node->getPath();
                }

                $table->addRow(array(
                    $type,
                    $property->getName(),
                    implode("\n", $nodePaths),
                ));
            }
        }

        $table->render($output);
    }
}

