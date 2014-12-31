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

class NodeReferencesCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:references');
        $this->setDescription('Returns all REFERENCE properties that refer to this node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of node (can include wildcard)');
        $this->addArgument('name', InputArgument::OPTIONAL, 'Limit references to given name');
        $this->setHelp(<<<HERE
This command returns all REFERENCE properties that refer to this node,
have the specified name and that are accessible through the current
Session.

If the <info>name</info> parameter is null then all referring REFERENCES are returned
regardless of name.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $path = $input->getArgument('path');
        $name = $input->getArgument('name');

        $nodes = $session->findNodes($path);

        foreach ($nodes as $node) {
            $references = array(
                'weak' => array(),
                'strong' => array(),
            );

            $references['weak'] = $node->getWeakReferences($name ?: null);
            $references['strong'] = $node->getReferences($name ?: null);

            $table = $this->get('helper.table')->create();
            $table->setHeaders(array(
                'Path', 'Property', 'Type',
            ));

            foreach ($references as $type => $typeReferences) {
                foreach ($typeReferences as $property) {
                    $nodePath = $property->getParent()->getPath();

                    $table->addRow(array(
                        $nodePath,
                        $property->getName(),
                        $type,
                    ));
                }
            }

            if (0 !== count($references['weak']) || 0 !== count($references['strong'])) {
                $output->writeln('<path>' . $node->getPath() . '</path>');
                $table->render($output);
            }
        }
    }
}
