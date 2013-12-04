<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use PHPCR\SessionInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Shell\Console\Helper\ResultFormatterHelper;
use PHPCR\Util\Console\Helper\PhpcrHelper;
use PHPCR\Util\Console\Helper\PhpcrCliHelper;
use PHPCR\Util\Console\Command\NodeDumpCommand;
use PHPCR\Shell\Console\Command\Query\SelectCommand;
use PHPCR\Util\Console\Command\NodeMoveCommand;
use PHPCR\Util\Console\Command\NodeRemoveCommand;
use PHPCR\Util\Console\Command\NodesUpdateCommand;
use PHPCR\Util\Console\Command\NodeTouchCommand;
use PHPCR\Util\Console\Command\NodeTypeListCommand;
use PHPCR\Util\Console\Command\NodeTypeRegisterCommand;
use PHPCR\Util\Console\Command\WorkspaceCreateCommand;
use PHPCR\Util\Console\Command\WorkspaceDeleteCommand;
use PHPCR\Util\Console\Command\WorkspaceExportCommand;
use PHPCR\Util\Console\Command\WorkspaceImportCommand;
use PHPCR\Util\Console\Command\WorkspaceListCommand;
use PHPCR\Util\Console\Command\WorkspacePurgeCommand;
use Symfony\Component\Console\Command\Command;
use PHPCR\Shell\Console\Command\Shell\ChangePathCommand;
use PHPCR\Shell\Console\Command\Shell\PwdCommand;
use Symfony\Component\Console\Input\ArrayInput;

class ShellApplication extends Application
{
    public function __construct(SessionInterface $session)
    {
        parent::__construct('PHPCR', '1.0');

        $this->add(new SelectCommand());
        $this->add(new ChangePathCommand());
        $this->add(new PwdCommand());

        $this->add($this->wrap(new NodeDumpCommand())
            ->setName('ls')
            ->setDescription('Alias for dump')
        );
        $ls = $this->get('ls');
        $ls->getDefinition()->getArgument('identifier')->setDefault(null);

        $this->add($this->wrap(new NodeMoveCommand())
            ->setName('mv')
        );
        $this->add($this->wrap(new NodeRemoveCommand())
            ->setName('rm')
        );
        $this->add($this->wrap(new NodesUpdateCommand())
            ->setName('update')
        );
        $this->add($this->wrap(new NodeTouchCommand())
            ->setName('touch')
        );
        $this->add($this->wrap(new NodeTypeListCommand())
            ->setName('nt-list')
        );
        $this->add($this->wrap(new NodeTypeRegisterCommand())
            ->setName('nt-register')
        );
        $this->add($this->wrap(new WorkspaceCreateCommand())
            ->setName('workspace-create')
        );
        $this->add($this->wrap(new WorkspaceDeleteCommand())
            ->setName('workspace-delete')
        );
        $this->add($this->wrap(new WorkspaceExportCommand())
            ->setName('workspace-export')
        );
        $this->add($this->wrap(new WorkspaceImportCommand())
            ->setName('workspace-import')
        );
        $this->add($this->wrap(new WorkspaceListCommand())
            ->setName('workspace-list')
        );
        $this->add($this->wrap(new WorkspacePurgeCommand())
            ->setName('workspace-purge')
        );

        $this->getHelperSet()->set(new PhpcrConsoleDumperHelper());
        $this->getHelperSet()->set(new ResultFormatterHelper());
        $this->getHelperSet()->set(new PhpcrHelper($session));
        $this->getHelperSet()->set(new PhpcrCliHelper($session));
    }

    public function wrap(Command $command)
    {
        return $command;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $name = $this->getCommandName($input);

        if (!$name) {
            $input = new ArrayInput(array('command' => 'pwd'));
        }

        return parent::doRun($input, $output);
    }
}
