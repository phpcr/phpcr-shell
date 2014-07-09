<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class VersionCheckpointCommand extends Command
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
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $nodeHelper = $this->getHelper('node');
        $path = $input->getArgument('path');
        $workspace = $session->getWorkspace();

        $node = $session->getNodeByPathOrIdentifier($path);
        $nodeHelper->assertNodeIsVersionable($node);
        $versionManager = $workspace->getVersionManager();
        $version = $versionManager->checkpoint($path);

        $output->writeln('Version: ' . $version->getName());
    }
}
