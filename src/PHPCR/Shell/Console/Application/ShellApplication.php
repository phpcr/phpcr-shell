<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Jackalope\NotImplementedException;
use PHPCR\Shell\Console\Command\Phpcr\AccessControlPrivilegeListCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockInfoCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockLockCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockRefreshCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockTokenAddCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockTokenListCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockTokenRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\LockUnlockCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeCorrespondingCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeCreateCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeDefinitionCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeInfoCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeLifecycleFollowCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeLifecycleListCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeListCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeMixinAddCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeMixinRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeOrderBeforeCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeReferencesCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeRenameCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodePropertySetCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeSetPrimaryTypeCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeSharedRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeSharedShowCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeTypeEditCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeTypeListCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeTypeLoadCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeTypeShowCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeTypeUnregisterCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeUpdateCommand;
use PHPCR\Shell\Console\Command\Phpcr\PhpcrShellCommand;
use PHPCR\Shell\Console\Command\Phpcr\QueryCommand;
use PHPCR\Shell\Console\Command\Phpcr\QuerySelectCommand;
use PHPCR\Shell\Console\Command\Phpcr\RepositoryDescriptorListCommand;
use PHPCR\Shell\Console\Command\Phpcr\RetentionHoldAddCommand;
use PHPCR\Shell\Console\Command\Phpcr\RetentionHoldListCommand;
use PHPCR\Shell\Console\Command\Phpcr\RetentionHoldRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\RetentionPolicyGetCommand;
use PHPCR\Shell\Console\Command\Phpcr\RetentionPolicyRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionExportViewCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionImpersonateCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionImportXMLCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionInfoCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionLoginCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionLogoutCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionNamespaceListCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionNamespaceSetCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeMoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionNodeShowCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodePropertyEditCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodePropertyRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodePropertyShowCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionRefreshCommand;
use PHPCR\Shell\Console\Command\Phpcr\SessionSaveCommand;
use PHPCR\Shell\Console\Command\Phpcr\VersionCheckinCommand;
use PHPCR\Shell\Console\Command\Phpcr\VersionCheckoutCommand;
use PHPCR\Shell\Console\Command\Phpcr\VersionCheckpointCommand;
use PHPCR\Shell\Console\Command\Phpcr\VersionHistoryCommand;
use PHPCR\Shell\Console\Command\Phpcr\VersionRemoveCommand;
use PHPCR\Shell\Console\Command\Phpcr\VersionRestoreCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceCreateCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceDeleteCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceListCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceNamespaceListCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceNamespaceRegisterCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceNamespaceUnregisterCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeCloneCommand;
use PHPCR\Shell\Console\Command\Phpcr\NodeCopyCommand;
use PHPCR\Shell\Console\Command\Phpcr\WorkspaceUseCommand;
use PHPCR\Shell\Console\Command\Shell\PathChangeCommand;
use PHPCR\Shell\Console\Command\Shell\PathShowCommand;
use PHPCR\Shell\Console\Command\Shell\ExitCommand;
use PHPCR\Shell\Console\Command\Shell\ListTreeCommand;
use PHPCR\Shell\Console\Helper\EditorHelper;
use PHPCR\Shell\Console\Helper\NodeHelper;
use PHPCR\Shell\Console\Helper\PathHelper;
use PHPCR\Shell\Console\Helper\RepositoryHelper;
use PHPCR\Shell\Console\Helper\ResultFormatterHelper;
use PHPCR\Shell\Console\Helper\TextHelper;
use PHPCR\Shell\PhpcrSession;
use PHPCR\SimpleCredentials;
use PHPCR\Util\Console\Command\NodeDumpCommand;
use PHPCR\Util\Console\Command\NodeTouchCommand;
use PHPCR\Util\Console\Command\NodeTypeRegisterCommand;
use PHPCR\Util\Console\Command\NodesUpdateCommand;
use PHPCR\Util\Console\Command\WorkspacePurgeCommand;
use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Util\Console\Helper\PhpcrHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArrayInput;

class ShellApplication extends Application
{
    protected $transports;
    protected $initialized;
    protected $sessionInput;

    /**
     * @var SessionInterface
     */
    private $session;

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

        $session = $this->initSession();

        $this->getHelperSet()->set(new EditorHelper($this->session));
        $this->getHelperSet()->set(new PhpcrConsoleDumperHelper());
        $this->getHelperSet()->set(new PhpcrHelper($this->session));
        $this->getHelperSet()->set(new ResultFormatterHelper());
        $this->getHelperSet()->set(new TextHelper());
        $this->getHelperSet()->set(new NodeHelper($this->session));
        $this->getHelperSet()->set(new PathHelper($this->session));
        $this->getHelperSet()->set(new RepositoryHelper($this->session->getRepository()));

        // add new commands
        $this->add(new AccessControlPrivilegeListCommand());
        $this->add(new RepositoryDescriptorListCommand());
        $this->add(new SessionExportViewCommand());
        $this->add(new SessionImpersonateCommand());
        $this->add(new SessionImportXMLCommand());
        $this->add(new SessionInfoCommand());
        $this->add(new SessionLoginCommand());
        $this->add(new SessionLogoutCommand());
        $this->add(new SessionNamespaceListCommand());
        $this->add(new SessionNamespaceSetCommand());
        $this->add(new NodePropertyEditCommand());
        $this->add(new NodePropertyRemoveCommand());
        $this->add(new NodePropertyShowCommand());
        $this->add(new SessionRefreshCommand());
        $this->add(new SessionSaveCommand());
        $this->add(new QuerySelectCommand());
        $this->add(new QueryCommand());
        $this->add(new RetentionHoldAddCommand());
        $this->add(new RetentionHoldListCommand());
        $this->add(new RetentionHoldRemoveCommand());
        $this->add(new RetentionPolicyGetCommand());
        $this->add(new RetentionPolicyRemoveCommand());
        $this->add(new WorkspaceCreateCommand());
        $this->add(new WorkspaceDeleteCommand());
        $this->add(new WorkspaceListCommand());
        $this->add(new NodeCloneCommand());
        $this->add(new NodeCopyCommand());
        $this->add(new WorkspaceNamespaceListCommand());
        $this->add(new WorkspaceNamespaceRegisterCommand());
        $this->add(new WorkspaceNamespaceUnregisterCommand());
        $this->add(new WorkspaceUseCommand());
        $this->add(new NodeTypeShowCommand());
        $this->add(new NodeTypeEditCommand());
        $this->add(new NodeTypeUnregisterCommand());
        $this->add(new NodeTypeListCommand());
        $this->add(new NodeTypeLoadCommand());
        $this->add(new VersionCheckoutCommand());
        $this->add(new VersionHistoryCommand());
        $this->add(new VersionRestoreCommand());
        $this->add(new VersionRemoveCommand());
        $this->add(new VersionCheckpointCommand());
        $this->add(new VersionCheckinCommand());
        $this->add(new NodeCreateCommand());
        $this->add(new NodeCorrespondingCommand());
        $this->add(new NodeDefinitionCommand());
        $this->add(new NodePropertySetCommand());
        $this->add(new NodeSetPrimaryTypeCommand());
        $this->add(new NodeRenameCommand());
        $this->add(new NodeMoveCommand());
        $this->add(new NodeMixinAddCommand());
        $this->add(new NodeMixinRemoveCommand());
        $this->add(new NodeOrderBeforeCommand());
        $this->add(new NodeInfoCommand());
        $this->add(new NodeLifecycleFollowCommand());
        $this->add(new NodeLifecycleListCommand());
        $this->add(new NodeListCommand());
        $this->add(new NodeUpdateCommand());
        $this->add(new NodeReferencesCommand());
        $this->add(new NodeSharedShowCommand());
        $this->add(new NodeSharedRemoveCommand());
        $this->add(new NodeRemoveCommand());
        $this->add(new LockLockCommand());
        $this->add(new LockInfoCommand());
        $this->add(new LockRefreshCommand());
        $this->add(new LockTokenAddCommand());
        $this->add(new LockTokenListCommand());
        $this->add(new LockTokenRemoveCommand());
        $this->add(new LockUnlockCommand());

        // add shell-specific commands
        $this->add(new PathChangeCommand());
        $this->add(new PathShowCommand());
        $this->add(new ExitCommand());

        // wrap phpcr-util commands
        $this->add($this->wrap(new NodeDumpCommand())
            ->setName('dump')
            ->setDescription('Alias for dump')
        );
        $ls = $this->get('dump');
        $ls->getDefinition()->getArgument('identifier')->setDefault(null);

        $this->add($this->wrap(new NodeListCommand())
            ->setName('ls')
        );
        $this->add($this->wrap(new PathChangeCommand())
            ->setName('cd')
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
        $this->add($this->wrap(new NodeTypeRegisterCommand())
            ->setName('nt-register')
        );
        $this->add($this->wrap(new WorkspacePurgeCommand())
            ->setName('workspace-purge')
        );

        $this->initialized = true;
    }

    private function initSession()
    {
        $transport = $this->getTransport($this->sessionInput);
        $repository = $transport->getRepository();
        $credentials = new SimpleCredentials(
            $this->sessionInput->getOption('phpcr-username'),
            $this->sessionInput->getOption('phpcr-password')
        );

        $session = $repository->login($credentials, $this->sessionInput->getOption('phpcr-workspace'));

        if (!$this->session) {
            $this->session = new PhpcrSession($session);
        } else {
            $this->session->setPhpcrSession($session);
        }
    }

    /**
     * Change the current workspace
     *
     * @param string $workspaceName
     */
    public function changeWorkspace($workspaceName)
    {
        $this->session->logout();
        $this->sessionInput->setOption('phpcr-workspace', $workspaceName);
        $this->initSession($this->sessionInput);
    }

    public function relogin($username, $password, $workspaceName = null)
    {
        $this->session->logout();
        $this->sessionInput->setOption('phpcr-username', $username);
        $this->sessionInput->setOption('phpcr-password', $password);

        if ($workspaceName) {
            $this->sessionInput->setOption('phpcr-workspace', $workspaceName);
        }
        $this->initSession($this->sessionInput);
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

    private function configureFormatter(OutputFormatter $formatter)
    {
        $style = new OutputFormatterStyle(null, null, array('bold'));
        $formatter->setStyle('node', $style);

        $style = new OutputFormatterStyle(null, null, array());
        $formatter->setStyle('property', $style);

        $style = new OutputFormatterStyle('magenta', null, array('bold'));
        $formatter->setStyle('node-type', $style);

        $style = new OutputFormatterStyle('magenta', null, array());
        $formatter->setStyle('property-type', $style);

        $style = new OutputFormatterStyle(null, null, array());
        $formatter->setStyle('property-value', $style);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->init();
        $this->configureFormatter($output->getFormatter());

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

            if ($e instanceof NotImplementedException) {
                throw new \Exception('Not implemented: ' . $e->getMessage());
            }

            $output->writeln('<error>(' . get_class($e) .') ' . $e->getMessage() . '</error>');
            return 1;
        }

        return $exitCode;
    }

    public function renderException($e, $output)
    {
        do {
            $output->writeln(sprintf('<fg=red>%s</fg=red>', $e->getMessage()));
        } while ($e = $e->getPrevious());
    }

    public function add(Command $command)
    {
        if ($command instanceof PhpcrShellCommand) {
            $showUnsupported = $this->sessionInput->getOption('unsupported');

            if ($showUnsupported || $command->isSupported($this->getHelperSet()->get('repository'))) {
                return parent::add($command);
            }
        } else {
            parent::add($command);
        }
    }
}
