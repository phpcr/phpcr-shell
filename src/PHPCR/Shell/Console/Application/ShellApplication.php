<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

use PHPCR\SimpleCredentials;

use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Util\Console\Helper\PhpcrHelper;

use PHPCR\Shell\Console\Command\Phpcr as CommandPhpcr;
use PHPCR\Shell\Console\Command\Shell as CommandShell;
use PHPCR\Shell\Console\Helper\ConfigHelper;
use PHPCR\Shell\Console\Helper\EditorHelper;
use PHPCR\Shell\Console\Helper\NodeHelper;
use PHPCR\Shell\Console\Helper\PathHelper;
use PHPCR\Shell\Console\Helper\RepositoryHelper;
use PHPCR\Shell\Console\Helper\ResultFormatterHelper;
use PHPCR\Shell\Console\Helper\TextHelper;

use PHPCR\Shell\Subscriber;
use PHPCR\Shell\Event;
use PHPCR\Shell\PhpcrSession;
use Symfony\Component\EventDispatcher\EventDispatcher;
use PHPCR\Shell\Event\PhpcrShellEvents;

/**
 * Main application for PHPCRSH
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ShellApplication extends Application
{
    /**
     * @var \PHPCR\TransportInterface[]
     */
    protected $transports;

    /**
     * @var boolean
     */
    protected $initialized;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $sessionInput;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $verpion);

        $this->dispatcher = new EventDispatcher();
    }


    /**
     * The SessionInput is the input used to intialize the shell.
     * It contains the connection parameters.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    public function setSessionInput(InputInterface $input)
    {
        $this->sessionInput = $input;
    }

    /**
     * Initialize the application
     */
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

        $this->initializeTransports();
        $this->initSession();
        $this->registerHelpers();
        $this->registerCommands();
        $this->registerEventListeners();

        $this->initialized = true;
    }

    /**
     * Initialize the supported transports.
     */
    private function initializeTransports()
    {
        $transports = array(
            new \PHPCR\Shell\Transport\DoctrineDbal($this->sessionInput),
            new \PHPCR\Shell\Transport\Jackrabbit($this->sessionInput),
        );

        foreach ($transports as $transport) {
            $this->transports[$transport->getName()] = $transport;;
        }
    }

    /**
     * Register the helpers required by the application
     */
    private function registerHelpers()
    {
        $helpers = array(
            new ConfigHelper(),
            new EditorHelper($this->session),
            new NodeHelper($this->session),
            new PathHelper($this->session),
            new PhpcrConsoleDumperHelper(),
            new PhpcrHelper($this->session),
            new RepositoryHelper($this->session->getRepository()),
            new ResultFormatterHelper(),
            new TextHelper(),
        );

        foreach ($helpers as $helper) {
            $this->getHelperSet()->set($helper);
        }
    }

    /**
     * Register the commands used in the shell
     */
    private function registerCommands()
    {
        // phpcr commands
        $this->add(new CommandPhpcr\AccessControlPrivilegeListCommand());
        $this->add(new CommandPhpcr\RepositoryDescriptorListCommand());
        $this->add(new CommandPhpcr\SessionExportViewCommand());
        $this->add(new CommandPhpcr\SessionImpersonateCommand());
        $this->add(new CommandPhpcr\SessionImportXMLCommand());
        $this->add(new CommandPhpcr\SessionInfoCommand());
        $this->add(new CommandPhpcr\SessionLoginCommand());
        $this->add(new CommandPhpcr\SessionLogoutCommand());
        $this->add(new CommandPhpcr\SessionNamespaceListCommand());
        $this->add(new CommandPhpcr\SessionNamespaceSetCommand());
        $this->add(new CommandPhpcr\NodePropertyEditCommand());
        $this->add(new CommandPhpcr\NodePropertyRemoveCommand());
        $this->add(new CommandPhpcr\NodePropertyShowCommand());
        $this->add(new CommandPhpcr\SessionRefreshCommand());
        $this->add(new CommandPhpcr\SessionSaveCommand());
        $this->add(new CommandPhpcr\QuerySelectCommand());
        $this->add(new CommandPhpcr\QueryCommand());
        $this->add(new CommandPhpcr\RetentionHoldAddCommand());
        $this->add(new CommandPhpcr\RetentionHoldListCommand());
        $this->add(new CommandPhpcr\RetentionHoldRemoveCommand());
        $this->add(new CommandPhpcr\RetentionPolicyGetCommand());
        $this->add(new CommandPhpcr\RetentionPolicyRemoveCommand());
        $this->add(new CommandPhpcr\WorkspaceCreateCommand());
        $this->add(new CommandPhpcr\WorkspaceDeleteCommand());
        $this->add(new CommandPhpcr\WorkspaceListCommand());
        $this->add(new CommandPhpcr\NodeCloneCommand());
        $this->add(new CommandPhpcr\NodeCopyCommand());
        $this->add(new CommandPhpcr\WorkspaceNamespaceListCommand());
        $this->add(new CommandPhpcr\WorkspaceNamespaceRegisterCommand());
        $this->add(new CommandPhpcr\WorkspaceNamespaceUnregisterCommand());
        $this->add(new CommandPhpcr\WorkspaceUseCommand());
        $this->add(new CommandPhpcr\NodeTypeShowCommand());
        $this->add(new CommandPhpcr\NodeTypeEditCommand());
        $this->add(new CommandPhpcr\NodeTypeUnregisterCommand());
        $this->add(new CommandPhpcr\NodeTypeListCommand());
        $this->add(new CommandPhpcr\NodeTypeLoadCommand());
        $this->add(new CommandPhpcr\VersionCheckoutCommand());
        $this->add(new CommandPhpcr\VersionHistoryCommand());
        $this->add(new CommandPhpcr\VersionRestoreCommand());
        $this->add(new CommandPhpcr\VersionRemoveCommand());
        $this->add(new CommandPhpcr\VersionCheckpointCommand());
        $this->add(new CommandPhpcr\VersionCheckinCommand());
        $this->add(new CommandPhpcr\NodeCreateCommand());
        $this->add(new CommandPhpcr\NodeCorrespondingCommand());
        $this->add(new CommandPhpcr\NodeDefinitionCommand());
        $this->add(new CommandPhpcr\NodePropertySetCommand());
        $this->add(new CommandPhpcr\NodeSetPrimaryTypeCommand());
        $this->add(new CommandPhpcr\NodeRenameCommand());
        $this->add(new CommandPhpcr\NodeMoveCommand());
        $this->add(new CommandPhpcr\NodeMixinAddCommand());
        $this->add(new CommandPhpcr\NodeMixinRemoveCommand());
        $this->add(new CommandPhpcr\NodeOrderBeforeCommand());
        $this->add(new CommandPhpcr\NodeInfoCommand());
        $this->add(new CommandPhpcr\NodeLifecycleFollowCommand());
        $this->add(new CommandPhpcr\NodeLifecycleListCommand());
        $this->add(new CommandPhpcr\NodeListCommand());
        $this->add(new CommandPhpcr\NodeUpdateCommand());
        $this->add(new CommandPhpcr\NodeReferencesCommand());
        $this->add(new CommandPhpcr\NodeSharedShowCommand());
        $this->add(new CommandPhpcr\NodeSharedRemoveCommand());
        $this->add(new CommandPhpcr\NodeRemoveCommand());
        $this->add(new CommandPhpcr\LockLockCommand());
        $this->add(new CommandPhpcr\LockInfoCommand());
        $this->add(new CommandPhpcr\LockRefreshCommand());
        $this->add(new CommandPhpcr\LockTokenAddCommand());
        $this->add(new CommandPhpcr\LockTokenListCommand());
        $this->add(new CommandPhpcr\LockTokenRemoveCommand());
        $this->add(new CommandPhpcr\LockUnlockCommand());

        // add shell-specific commands
        $this->add(new CommandShell\ConfigInitCommand());
        $this->add(new CommandShell\PathChangeCommand());
        $this->add(new CommandShell\PathShowCommand());
        $this->add(new CommandShell\ExitCommand());
    }

    private function registerEventListeners()
    {
        $this->dispatcher->addSubscriber(new Subscriber\ExceptionSubscriber());
    }

    /**
     * Initialize the PHPCR session
     */
    private function initSession()
    {
        $transport = $this->getTransport();
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
     * @todo: Move to session helper?
     *
     * @param string $workspaceName
     */
    public function changeWorkspace($workspaceName)
    {
        $this->session->logout();
        $this->sessionInput->setOption('phpcr-workspace', $workspaceName);
        $this->initSession($this->sessionInput);
    }

    /**
     * Login (again)
     *
     * @todo: Move to session helper
     *
     * @param string $username
     * @param string $password
     * @param string $workspaceName
     */
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

    /**
     * Return the transport as defined in the sessionInput
     */
    private function getTransport()
    {
        $transportName = $this->sessionInput->getOption('transport');

        if (!isset($this->transports[$transportName])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown transport "%s", I have "%s"',
                $transportName, implode(', ', array_keys($this->transports))
            ));
        }

        $transport = $this->transports[$transportName];

        return $transport;
    }

    /**
     * Configure the output formatter
     */
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

        $style = new OutputFormatterStyle(null, 'red', array());
        $formatter->setStyle('exception', $style);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->init();

        // configure the formatter for the output
        $this->configureFormatter($output->getFormatter());

        $name = $this->getCommandName($input);

        $event = new Event\CommandPreRunEvent($name, $input);
        $this->dispatcher->dispatch(PhpcrShellEvents::COMMAND_PRE_RUN, $event);
        $input = $event->getInput();

        if (!$name) {
            $input = new ArrayInput(array('command' => 'shell:path:show'));
        }

        try {
            $exitCode = parent::doRun($input, $output);
        } catch (\Exception $e) {
            $this->dispatcher->dispatch(PhpcrShellEvents::COMMAND_EXCEPTION, new Event\CommandExceptionEvent($e, $output));
            return 1;
        }

        return $exitCode;
    }

    /**
     * {@inheritDoc}
     *
     * Render an exception to the console
     *
     * @access public
     *
     * @param \Exception $e $exception
     * @param OutputInterface $output
     */
    public function renderException(\Exception $exception, OutputInterface $output)
    {
        $output->writeln(sprintf('<exception>%s</exception>', $exception->getMessage()));
    }

    /**
     * {@inheritDoc}
     *
     * Wrap the add method and do not register commands which are unsupported by
     * the current transport.
     *
     * @param Command $command
     */
    public function add(Command $command)
    {
        if ($command instanceof PhpcrShellCommand) {
            $showUnsupported = $this->sessionInput->getOption('unsupported');

            if ($showUnsupported || $command->isSupported($this->getHelperSet()->get('repository'))) {
                parent::add($command);
            }
        } else {
            parent::add($command);
        }
    }
}
