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

class VersionHistoryCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('version:history');
        $this->setDescription('Show version history of node at given absolute path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
Lists the version history of the node given at <info>path</info>.
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_VERSIONING_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $nodeHelper = $this->get('helper.node');
        $table = $this->get('helper.table')->create();

        $path = $input->getArgument('path');
        $workspace = $session->getWorkspace();

        $node = $session->getNodeByPathOrIdentifier($path);
        $nodeHelper->assertNodeIsVersionable($node);
        $versionManager = $workspace->getVersionManager();
        $history = $versionManager->getVersionHistory($path);

        $versions = $history->getAllVersions();

        $table->setHeaders(array('Name', 'Created'));

        foreach ($versions as $name => $version) {
            $table->addRow(array(
                $name,
                $version->getCreated()->format('Y-m-d H:i:s'),
            ));
        }

        $table->render($output);
    }
}
