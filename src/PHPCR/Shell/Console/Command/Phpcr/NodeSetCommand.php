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
use Symfony\Component\Console\Input\InputOption;
use PHPCR\PropertyType;

class NodeSetCommand extends Command
{
    protected function configure()
    {
        $this->setName('node:set');
        $this->setDescription('Rename the node at the current path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path of property - can include the node name');
        $this->addArgument('value', null, InputArgument::OPTIONAL, null, 'Value for named property');
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of named property');
        $this->setHelp(<<<HERE
Defines a value for a property identified by its name.

Sets the property of this node called <info>name</info> to the specified value.
This method works as factory method to create new properties and as a
shortcut for PropertyInterface::setValue()

If the property does not yet exist, it is created and its property type
determined by the node type of this node. If, based on the name and
value passed, there is more than one property definition that applies,
the repository chooses one definition according to some implementation-
specific criteria.

Once property with name P has been created, the behavior of a subsequent
<info>node:set</info> may differ across implementations. Some repositories
may allow P to be dynamically re-bound to a different property
definition (based for example, on the new value being of a different
type than the original value) while other repositories may not allow
such dynamic re-binding.

Passing a null as the second parameter removes the property. It is
equivalent to calling remove on the Property object itself. For example,
<info>node:set P</info>  would remove property called "P" of the
current node.

This is a session-write method, meaning that changes made through this
method are dispatched on SessionInterface::save().

If <info>type</info> is given:
The behavior of this method is identical to that of <info>node:set prop
value</info> except that the intended property type is explicitly specified.

<b>Note:</b>
Have a look at the JSR-283 spec and/or API documentation for more details
on what is supposed to happen for different types of values being passed
to this method.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $pathHelper = $this->getHelper('path');
        $path = $session->getAbsPath($input->getArgument('path'));
        $value = $input->getArgument('value');
        $type = $input->getOption('type');

        $intType = null;
        if ($type) {
            $intType = PropertyType::valueFromName($type);
        }

        $nodePath = $pathHelper->getParentPath($path);
        $propName = $pathHelper->getNodeName($path);
        $currentNode = $session->getNode($nodePath);

        if ($intType) {
            $currentNode->setProperty($propName, $value, $intType);
        } else {
            $currentNode->setProperty($propName, $value);
        }
    }
}
