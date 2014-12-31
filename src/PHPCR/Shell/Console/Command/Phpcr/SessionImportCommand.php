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
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\PathHelper;

class SessionImportCommand extends BasePhpcrCommand
{
    protected $uuidBehaviors = array(
        'create-new',
        'collision-remove-existing',
        'collision-replace-existing',
        'collision-throw',
    );

    protected function configure()
    {
        $this->setName('session:import');
        $this->setDescription('Import content from an XML file');
        $this->addArgument('parentAbsPath', InputArgument::REQUIRED, 'Path of node to export');
        $this->addArgument('file', InputArgument::REQUIRED, 'File to import from');
        $this->addOption('uuid-behavior', null, InputOption::VALUE_REQUIRED, 'UUID behavior', 'create-new');
        $this->setHelp(<<<HERE
Import the specified XML document into the current session as a child of the node specified
by the <info>parentAbsPath</info> argument.

If the incoming XML does not appear to be a JCR system view XML document
then it is interpreted as a document view XML document.

The option <info>uuid-behavior</info> governs how the identifiers of incoming nodes are
handled. There are four options:

- <info>import-uuid-create-new<info>: Incoming nodes are added
     in the same way that new node is added with Node::addNode(). That
     is, they are either assigned newly created identifiers upon
     addition or upon save (depending on the implementation, see 4.9.1.1
     When Identifiers are Assigned in the specification). In either
     case, identifier collisions will not occur.
     (Weak)references will point to the original node if existing, to
     the imported node with matching id otherwise.
- <info>import-uuid-collision-remove-existing</info>: If an
     incoming node has the same identifier as a node already existing in
     the workspace then the already existing node (and its subgraph) is
     removed from wherever it may be in the workspace before the
     incoming node is added. Note that this can result in nodes
     "disappearing" from locations in the workspace that are remote from
     the location to which the incoming subgraph is being written. Both
     the removal and the new addition will be dispatched on save.
- <info>import-uuid-collision-replace-existing</info>: If an
     incoming node has the same identifier as a node already existing in
     the workspace, then the already-existing node is replaced by the
     incoming node in the same position as the existing node. Note that
     this may result in the incoming subgraph being disaggregated and
     "spread around" to different locations in the workspace. In the
     most extreme case this behavior may result in no node at all being
     added as child of parentAbsPath. This will occur if the topmost
     element of the incoming XML has the same identifier as an existing
     node elsewhere in the workspace. The change will be dispatched on
     save.
- <info>import-uuid-collision-throw</info>: If an incoming node
     has the same identifier as a node already existing in the workspace
     then an ItemExistsException is thrown.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $file = $input->getArgument('file');
        $parentAbsPath = $input->getArgument('parentAbsPath');
        $uuidBehavior = $input->getOption('uuid-behavior');

        if (!in_array($uuidBehavior, $this->uuidBehaviors)) {
            throw new \Exception(sprintf(
                "The specified uuid behavior \"%s\" is invalid, you should use one of:\n%s",
                $uuidBehavior,
                '    - ' . implode("\n    - ", $this->uuidBehaviors)
            ));
        }

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" does not exist', $file
            ));
        }

        PathHelper::assertValidAbsolutePath($parentAbsPath);

        $uuidBehavior = constant('\PHPCR\ImportUUIDBehaviorInterface::IMPORT_UUID_' . strtoupper(str_replace('-', '_', $uuidBehavior)));

        $session->importXml($parentAbsPath, $file, $uuidBehavior);
    }
}
