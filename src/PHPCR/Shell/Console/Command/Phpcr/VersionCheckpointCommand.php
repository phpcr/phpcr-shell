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

class VersionCheckpointCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('version:checkpoint');
        $this->setDescription('Checkin and then checkout a node');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to node');
        $this->setHelp(<<<HERE
Performs a <info>version:checkin</info> followed by a <info>version:checkout</info> on the versionable node at
<info>path</info>

If this node is already checked-in, this method is equivalent to <info>version:checkout</info>.
HERE
        );
        $this->requiresDescriptor(RepositoryInterface::OPTION_VERSIONING_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $nodeHelper = $this->get('helper.node');
        $path = $input->getArgument('path');
        $workspace = $session->getWorkspace();

        $node = $session->getNodeByPathOrIdentifier($path);
        $nodeHelper->assertNodeIsVersionable($node);
        $versionManager = $workspace->getVersionManager();
        $version = $versionManager->checkpoint($path);

        $output->writeln('Version: ' . $version->getName());
    }
}
