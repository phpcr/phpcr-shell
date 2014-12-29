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

class RetentionHoldListCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('retention:hold:list');
        $this->setDescription('List retention holds at given absolute path UNSUPPORTED');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to node to which we want to add a hold');
        $this->setHelp(<<<HERE
Lists all hold object names that have been added to the
existing node at <info>absPath</info>.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_RETENTION_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $retentionManager = $session->getRetentionManager();
        $absPath = $input->getArgument('absPath');

        $holds = $retentionManager->getHolds($absPath);
        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Name'));

        foreach ($holds as $hold) {
            $table->addRow(array($hold->getName()));
        }

        $table->render($output);
    }
}
