<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use PHPCR\SessionInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PHPCR\Shell\Console\Command\Query\SelectCommand;
use PHPCR\Shell\Console\Command\Shell\ChangePathCommand;
use PHPCR\Shell\Console\Command\Shell\PwdCommand;
use PHPCR\Shell\Console\Helper\ResultFormatterHelper;
use PHPCR\Shell\PhpcrSession;
use PHPCR\SimpleCredentials;
use PHPCR\Util\Console\Command\NodeDumpCommand;
use PHPCR\Util\Console\Command\NodeMoveCommand;
use PHPCR\Util\Console\Command\NodeRemoveCommand;
use PHPCR\Util\Console\Command\NodeTouchCommand;
use PHPCR\Util\Console\Command\NodeTypeListCommand;
use PHPCR\Util\Console\Command\NodeTypeRegisterCommand;
use PHPCR\Util\Console\Command\NodesUpdateCommand;
use PHPCR\Util\Console\Command\WorkspaceCreateCommand;
use PHPCR\Util\Console\Command\WorkspaceDeleteCommand;
use PHPCR\Util\Console\Command\WorkspaceExportCommand;
use PHPCR\Util\Console\Command\WorkspaceImportCommand;
use PHPCR\Util\Console\Command\WorkspaceListCommand;
use PHPCR\Util\Console\Command\WorkspacePurgeCommand;
use PHPCR\Util\Console\Helper\PhpcrCliHelper;
use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Util\Console\Helper\PhpcrHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use PHPCR\Shell\Console\Command\Shell\ExitCommand;
use PHPCR\Shell\Console\TransportInterface;
use PHPCR\Shell\Console\Command\Shell\WorkspaceChangeCommand;

class ShellApplication extends Application
{
    protected $transports;
    protected $credentials;

    public function __construct(InputInterface $input, $transports = array())
    {
        parent::__construct('PHPCR', '1.0');

        // initialize transports
        foreach (array_merge(array(
            new \PHPCR\Shell\Transport\DoctrineDbal($input),
            new \PHPCR\Shell\Transport\Jackrabbit($input),
        ), $transports) as $transport) {
            $this->transports[$transport->getName()] = $transport;;
        }

        // add shell-specific commands
        $this->add(new SelectCommand());
        $this->add(new ChangePathCommand());
        $this->add(new PwdCommand());
        $this->add(new ExitCommand());

        // wrap phpcr-util commands
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

        $session = $this->getSession($input);

        $this->getHelperSet()->set(new PhpcrConsoleDumperHelper());
        $this->getHelperSet()->set(new ResultFormatterHelper());
        $this->getHelperSet()->set(new PhpcrHelper($session));
        $this->getHelperSet()->set(new PhpcrCliHelper($session));
    }

    private function getSession($input)
    {
        $transport = $this->getTransport($input);
        $repository = $transport->getRepository();
        $credentials = new SimpleCredentials(
            $input->getOption('phpcr-username'),
            $input->getOption('phpcr-password')
        );

        $session = $repository->login($credentials, $input->getOption('phpcr-workspace'));
        $session = new PhpcrSession($session);

        return $session;
    }

    public function changeWorkspace($workspace)
    {
    }

    private function getTransport(InputInterface $input)
    {
        $transportName = $input->getOption('transport');

        if (!isset($this->transports[$transportName])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown transport "%s", I have "%s"',
                $transportName, implode(', ', array_keys($this->transports))
            ));
        }

        $transport = $this->transports[$transportName];

        return $transport;
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
