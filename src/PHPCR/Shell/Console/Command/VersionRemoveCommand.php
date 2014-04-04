<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class VersionRemoveCommand extends Command
{
    protected function configure()
    {
        $this->setName('version:remove');
        $this->setDescription('Remove a node version');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to node');
        $this->addArgument('versionName', null, InputArgument::REQUIRED, 'Name of version to remove');
        $this->setHelp(<<<HERE
Removes the named version from this version history and automatically
repairs the version graph.

If the version to be removed is V, V's predecessor set is P and V's
successor set is S, then the version graph is repaired s follows:

- For each member of P, remove the reference to V from its successor
  list and add references to each member of S.
- For each member of S, remove the reference to V from its predecessor
  list and add references to each member of P.

<b>Note</b> that this change is made immediately; there is no need to
call save. In fact, since the the version storage is read-only with
respect to normal repository methods, save does not even function in
this context.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $absPath = $input->getArgument('absPath');
        $versionName = $input->getArgument('versionName');
        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();
        $versionManager = $workspace->getVersionManager();

        $history = $versionManager->getVersionHistory($absPath);
        $history->removeVersion($versionName);
    }
}
