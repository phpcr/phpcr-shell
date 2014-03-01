<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class VersionHistoryCommand extends Command
{
    protected function configure()
    {
        $this->setName('version:history');
        $this->setDescription('Show version history of node at given absolute path');
        $this->addArgument('absPath', null, InputArgument::REQUIRED, 'Absolute path to node');
        $this->setHelp(<<<HERE
Lists the version history of the node given at <info>absPath</info>.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $nodeHelper = $this->getHelper('node');
        $table = $this->getHelper('table');

        $absPath = $input->getArgument('absPath');
        $workspace = $session->getWorkspace();

        $node = $session->getNode($absPath);
        $nodeHelper->assertNodeIsVersionable($node);
        $versionManager = $workspace->getVersionManager();
        $history = $versionManager->getVersionHistory($absPath);

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
