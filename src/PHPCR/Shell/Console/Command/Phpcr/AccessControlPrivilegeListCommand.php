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

class AccessControlPrivilegeListCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('access-control:privilege:list');
        $this->setDescription('List the privileges of the repository or a specific node');
        $this->addArgument('absPath', InputArgument::OPTIONAL, 'Absolute path for node, optional.');
        $this->addOption('supported', null, InputOption::VALUE_NONE, 'List privileges supported by repository rather than current session.');
        $this->setHelp(<<<HERE
NOTE: This command is not supported by Jackrabbit.

List the privileges of the current session or the node at the given path.

List the privileges for the object specified by <info>abs-path</info>
argument.

By default the returned privileges are those for which
AccessControlManagerInterface::hasPrivileges() would return true.

The results reported by the this command reflect the net effect of the
currently applied control mechanisms. It does not reflect unsaved access
control policies or unsaved access control entries. Changes to access
control status caused by these mechanisms only take effect on
SessionInterface::save() and are only then reflected in the results of
the privilege test methods.

If the <info>--supported</info> option is supplied then the command does not
list the privileges held by the current session, but rather the privileges
supported by the repository.
HERE
    );

        $this->requiresDescriptor(RepositoryInterface::OPTION_ACCESS_CONTROL_SUPPORTED, true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $supported = $input->getOption('supported');
        $absOath = $input->getArgument('absPath');
        $acm = $session->getAccessControlManager();

        if (true === $supported) {
            $privileges = $acm->getSupportedPrivileges($absPath);
        } else {
            $privileges = $acm->getPrivileges($absPath);
        }

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Name'));

        foreach ($privileges as $privilege) {
            $table->addRow(array($privilege->getName()));
        }
    }
}
