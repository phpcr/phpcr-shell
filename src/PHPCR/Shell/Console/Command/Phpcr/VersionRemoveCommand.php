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
use PHPCR\RepositoryInterface;

class VersionRemoveCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('version:remove');
        $this->setDescription('Remove a node version');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to node');
        $this->addArgument('versionName', InputArgument::REQUIRED, 'Name of version to remove');
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
        $this->requiresDescriptor(RepositoryInterface::OPTION_VERSIONING_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');

        $versionName = $input->getArgument('versionName');
        $path = $session->getAbsPath($input->getArgument('path'));
        $workspace = $session->getWorkspace();
        $versionManager = $workspace->getVersionManager();

        $history = $versionManager->getVersionHistory($path);
        $history->removeVersion($versionName);
    }
}
