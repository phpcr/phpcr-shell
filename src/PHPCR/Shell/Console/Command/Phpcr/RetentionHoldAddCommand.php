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

class RetentionHoldAddCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('retention:hold:add');
        $this->setDescription('Adds a retention hold UNSUPPORTED');
        $this->addArgument('absPath', InputArgument::REQUIRED, 'Absolute path to node to which we want to add a hold');
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of hold to add');
        $this->addOption('deep', null, InputOption::VALUE_NONE, 'Apply hold also to the children of specified node.');
        $this->setHelp(<<<HERE
Places a hold on the existing node at <info>absPath</info>.

If the <info>is-deep</info> is true the hold applies to this node and its
subgraph. The hold does not take effect until a save is performed. A node may
have more than one hold. The format and interpretation of the name are not
specified. They are application-dependent.
HERE
        );

        $this->requiresDescriptor(RepositoryInterface::OPTION_RETENTION_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $retentionManager = $session->getRetentionManager();
        $absPath = $input->getArgument('absPath');
        $isDeep = $input->getOption('is-deep');
        $name = $input->getArgument('name');

        $retentionManager->addHold($absPath, $name, $isDeep);
    }
}
