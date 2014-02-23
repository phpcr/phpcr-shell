<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Util\Console\Helper\PhpcrHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use PHPCR\Shell\Console\Command\Shell\ExitCommand;
use PHPCR\Shell\Console\Command\Shell\ListTreeCommand;
use PHPCR\Shell\Console\Command\RepositoryDescriptorListCommand;
use PHPCR\Shell\Console\Command\SessionExportViewCommand;
use PHPCR\Shell\Console\Command\SessionImportXMLCommand;
use PHPCR\Shell\Console\Command\SessionInfoCommand;
use PHPCR\Shell\Console\Command\SessionLogoutCommand;
use PHPCR\Shell\Console\Command\SessionNamespaceListCommand;
use PHPCR\Shell\Console\Command\SessionNamespaceSetCommand;
use PHPCR\Shell\Console\Command\SessionNodeMoveCommand;
use PHPCR\Shell\Console\Command\SessionNodeShowCommand;
use PHPCR\Shell\Console\Helper\TextHelper;
use PHPCR\Shell\Console\Command\SessionPropertyRemoveCommand;
use PHPCR\Shell\Console\Command\SessionPropertyShowCommand;
use PHPCR\Shell\Console\Command\SessionPropertyEditCommand;
use PHPCR\Shell\Console\Command\SessionRefreshCommand;
use PHPCR\Shell\Console\Command\SessionSaveCommand;
use PHPCR\Shell\Console\Command\SessionImpersonateCommand;
use PHPCR\Shell\Console\Command\AccessControlPrivilegeListCommand;
use PHPCR\Shell\Console\Command\QuerySelectCommand;
use PHPCR\Shell\Console\Command\QueryCommand;

class ShellApplication extends Application
{
    protected $transports;
    protected $credentials;
    protected $initialized;
    protected $sessionInput;

    /**
     * Input from the shell command, containing the connection
     * parameters etc.
     */
    public function setSessionInput(InputInterface $input)
    {
        $this->sessionInput = $input;
    }

    public function init()
    {
        if (true === $this->initialized) {
            return;
        }

        if (null === $this->sessionInput) {
            throw new \RuntimeException(
                'sessionInput has not been set.'
            );
        }

        // initialize transports
        foreach (array(
            new \PHPCR\Shell\Transport\DoctrineDbal($this->sessionInput),
            new \PHPCR\Shell\Transport\Jackrabbit($this->sessionInput),
        ) as $transport) {
            $this->transports[$transport->getName()] = $transport;;
        }

        // add new commands
        $this->add(new AccessControlPrivilegeListCommand());
        $this->add(new RepositoryDescriptorListCommand());
        $this->add(new SessionExportViewCommand());
        $this->add(new SessionImpersonateCommand());
        $this->add(new SessionImportXMLCommand());
        $this->add(new SessionInfoCommand());
        $this->add(new SessionLogoutCommand());
        $this->add(new SessionNamespaceListCommand());
        $this->add(new SessionNamespaceSetCommand());
        $this->add(new SessionNodeMoveCommand());
        $this->add(new SessionNodeShowCommand());
        $this->add(new SessionPropertyEditCommand());
        $this->add(new SessionPropertyRemoveCommand());
        $this->add(new SessionPropertyShowCommand());
        $this->add(new SessionRefreshCommand());
        $this->add(new SessionSaveCommand());
        $this->add(new QuerySelectCommand());
        $this->add(new QueryCommand());

        // add shell-specific commands
        $this->add(new ChangePathCommand());
        $this->add(new PwdCommand());
        $this->add(new ExitCommand());

        // wrap phpcr-util commands
        $this->add($this->wrap(new NodeDumpCommand())
            ->setName('dump')
            ->setDescription('Alias for dump')
        );
        $ls = $this->get('dump');
        $ls->getDefinition()->getArgument('identifier')->setDefault(null);

        $this->add(new ListTreeCommand());

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

        $session = $this->getSession($this->sessionInput);

        $this->getHelperSet()->set(new PhpcrConsoleDumperHelper());
        $this->getHelperSet()->set(new ResultFormatterHelper());
        $this->getHelperSet()->set(new TextHelper());
        $this->getHelperSet()->set(new PhpcrHelper($session));

        $this->initialized = true;
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
        $this->init();

        $name = $this->getCommandName($input);

        if (!$name) {
            $input = new ArrayInput(array('command' => 'pwd'));
        }

        try {
            $exitCode = parent::doRun($input, $output);
        } catch (\Exception $e) {
            if (!$e->getMessage()) {
                if ($e instanceof \PHPCR\UnsupportedRepositoryOperationException) {
                    throw new \Exception('Unsupported repository operation');
                }

            }

            throw $e;
        }

        return $exitCode;
    }

    public function renderException($e, $output)
    {
        do {
            $output->writeln(sprintf('<fg=red>%s</fg=red>', $e->getMessage()));
        } while ($e = $e->getPrevious());
    }
}
