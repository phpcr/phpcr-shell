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
use PHPCR\RepositoryInterface;

class VersionRestoreCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('version:restore');
        $this->setDescription('Restore a node version');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to node');
        $this->addArgument('versionName', InputArgument::REQUIRED, 'Name of version to retore');
        $this->addOption('remove-existing', null, InputOption::VALUE_NONE, 'Flag that governs what happens in case of identifier collision');
        $this->setHelp(<<<HERE
Attempt to restore an old version of a node.

The <comment>versionName</comment> should correspond to a version name as revealed by
the <info>version:history</info> command.

If the restore succeeds the changes made are dispatched immediately;
there is no need to call save.
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_VERSIONING_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');

        $path = $session->getAbsPath($input->getArgument('path'));
        $versionName = $input->getArgument('versionName');
        $removeExisting = $input->getOption('remove-existing');
        $workspace = $session->getWorkspace();
        $versionManager = $workspace->getVersionManager();
        $versionManager->restore($removeExisting, $versionName, $path);
    }
}
