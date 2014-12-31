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
use PHPCR\PropertyType;
use PHPCR\PathNotFoundException;
use PHPCR\Util\UUIDHelper;

class NodePropertySetCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('node:property:set');
        $this->setDescription('Rename the node at the current path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of property - parent path can include wildcards');
        $this->addArgument('value', InputArgument::OPTIONAL, 'Value for named property');
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of named property');
        $this->setHelp(<<<HERE
Defines or set a value for a property identified by its name.

    PHPCRSH> node:property:set . propname "some value" --type="String"

You can also use wildcards:

    PHPCRSH> node:property:set * propname "some value"

This is a session-write method, meaning that changes made through this
method are dispatched on <info>session:save</info>.

Note that this command does NOT support multivalue, use <info>node:edit</info> instead.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $pathHelper = $this->get('helper.path');
        $path = $session->getAbsPath($input->getArgument('path'));
        $value = $input->getArgument('value');
        $type = $input->getOption('type');

        $nodePath = $pathHelper->getParentPath($path);
        $propName = $pathHelper->getNodeName($path);

        $nodes = $session->findNodes($nodePath);

        foreach ($nodes as $node) {
            $intType = null;

            if ($type) {
                $intType = PropertyType::valueFromName($type);

                if ($intType === PropertyType::REFERENCE  || $intType === PropertyType::WEAKREFERENCE) {
                    // convert path to UUID
                    if (false === UUIDHelper::isUuid($value)) {
                        $path = $value;
                        try {
                            $targetNode = $session->getNode($path);
                            $value = $targetNode->getIdentifier();
                        } catch (PathNotFoundException $e) {
                        }

                        if (null === $value) {
                            throw new \InvalidArgumentException(sprintf(
                                'Node at path "%s" specified for reference is not referenceable',
                                $path
                            ));
                        }
                    }
                }
            } else {
                try {
                    $property = $node->getProperty($propName);
                    $intType = $property->getType();
                } catch (PathNotFoundException $e) {
                    // property doesn't exist and no type specified, default to string
                    $intType = PropertyType::STRING;
                }
            }

            $node->setProperty($propName, $value, $intType);
        }
    }
}
