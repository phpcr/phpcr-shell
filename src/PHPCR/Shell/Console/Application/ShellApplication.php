<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

use PHPCR\Shell\Console\Command\Phpcr as CommandPhpcr;
use PHPCR\Shell\Console\Command\Shell as CommandShell;
use PHPCR\Shell\Console\Helper\ConfigHelper;
use PHPCR\Shell\Console\Helper\EditorHelper;
use PHPCR\Shell\Console\Helper\NodeHelper;
use PHPCR\Shell\Console\Helper\PathHelper;
use PHPCR\Shell\Console\Helper\RepositoryHelper;
use PHPCR\Shell\Console\Helper\ResultFormatterHelper;
use PHPCR\Shell\Console\Helper\TextHelper;
use PHPCR\Shell\Console\Helper\PhpcrHelper;

use PHPCR\Shell\Event;
use PHPCR\Shell\Event\ApplicationInitEvent;
use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Subscriber;
use PHPCR\Shell\Console\Command\Phpcr\PhpcrShellCommand;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\Transport\TransportRegistry;
use PHPCR\Shell\Config\ProfileLoader;
use PHPCR\Shell\Console\Helper\TableHelper;
use PHPCR\Shell\Console\Helper\SyntaxHighlighterHelper;

/**
 * Main application for PHPCRSH
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ShellApplication extends Application
{
    /**
     * True when application has been initialized once
     *
     * @var boolean
     */
    protected $initialized;

    /**
     * @var boolean
     */
    protected $showUnsupported = false;

    /**
     * Constructor - name and version inherited from SessionApplication
     *
     * {@inheritDoc}
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->profile = new Profile();
        $this->dispatcher = new EventDispatcher();
        $this->transportRegistry = new TransportRegistry();
        $this->registerTransports();
        $this->registerHelpers();
        $this->registerEventListeners();
    }

    /**
     * Initialize the supported transports.
     *
     * @access private
     */
    protected function registerTransports()
    {
        $transports = array(
            new \PHPCR\Shell\Transport\Transport\DoctrineDbal($this->profile),
            new \PHPCR\Shell\Transport\Transport\Jackrabbit($this->profile),
        );

        foreach ($transports as $transport) {
            $this->transportRegistry->register($transport);
        }
    }

    /**
     * If true, show all commands, even if they are unsupported by the
     * transport.
     *
     * @param boolean $boolean
     */
    public function setShowUnsupported($boolean)
    {
        $this->showUnsupported = $boolean;
    }

    /**
     * Initialize the application.
     *
     * Note that we do this "lazily" because we instantiate the ShellApplication early,
     * before the SessionInput has been set. The SessionInput must be set before we
     * can initialize the application.
     */
    public function init()
    {
        if (true === $this->initialized) {
            return;
        }

        $this->registerCommands();

        $event = new ApplicationInitEvent($this);
        $this->dispatcher->dispatch(PhpcrShellEvents::APPLICATION_INIT, $event);

        $this->initialized = true;
    }

    /**
     * Register the helpers required by the application
     */
    private function registerHelpers()
    {
        $phpcrHelper = new PhpcrHelper($this->transportRegistry, $this->profile);

        $helpers = array(
            new ConfigHelper(),
            new EditorHelper(),
            new NodeHelper(),
            new PathHelper(),
            new RepositoryHelper($phpcrHelper),
            new ResultFormatterHelper(),
            new TextHelper(),
            new TableHelper(),
            new SyntaxHighlighterHelper(),
            $phpcrHelper
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
        $this->add(new CommandPhpcr\QueryCommand());
        $this->add(new CommandPhpcr\QuerySelectCommand());
        $this->add(new CommandPhpcr\QueryUpdateCommand());
        $this->add(new CommandPhpcr\QueryDeleteCommand());
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
        $this->add(new CommandPhpcr\NodeShowCommand());
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
        $this->add(new CommandPhpcr\NodeFileImportCommand());
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
        $this->add(new CommandShell\AliasListCommand());
        $this->add(new CommandShell\ConfigInitCommand());
        $this->add(new CommandShell\ConfigReloadCommand());
        $this->add(new CommandShell\PathChangeCommand());
        $this->add(new CommandShell\PathShowCommand());
        $this->add(new CommandShell\ExitCommand());
    }

    private function registerEventListeners()
    {
        $this->dispatcher->addSubscriber(new Subscriber\ProfileFromSessionInputSubscriber());
        $this->dispatcher->addSubscriber(new Subscriber\ProfileWriterSubscriber(
            new ProfileLoader(
                $this->getHelperSet()->get('config')
            )
        ));
        $this->dispatcher->addSubscriber(new Subscriber\ProfileLoaderSubscriber(
            new ProfileLoader(
                $this->getHelperSet()->get('config')
            )
        ));

        $this->dispatcher->addSubscriber(new Subscriber\ConfigInitSubscriber());
        $this->dispatcher->addSubscriber(new Subscriber\ExceptionSubscriber());
        $this->dispatcher->addSubscriber(new Subscriber\AliasSubscriber($this->getHelperSet()));
    }

    /**
     * Configure the output formatter
     */
    private function configureFormatter(OutputFormatter $formatter)
    {
        $style = new OutputFormatterStyle(null, null, array('bold'));
        $formatter->setStyle('node', $style);

        $style = new OutputFormatterStyle('blue', null, array('bold'));
        $formatter->setStyle('templatenode', $style);

        $style = new OutputFormatterStyle('blue', null, array());
        $formatter->setStyle('templateproperty', $style);

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

        // syntax highlighting
        $colors = array(
            'html-tag' => 'magenta',
            'html-tagin' => 'yellow',
            'html-quote' => null,
        );

        foreach ($colors as $tag => $color) {
            $style = new OutputFormatterStyle($color, null, array());
            $formatter->setStyle($tag, $style);
        }
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
            $this->dispatcher->dispatch(PhpcrShellEvents::COMMAND_EXCEPTION, new Event\CommandExceptionEvent($e, $input, $output));

            return 1;
        }

        return $exitCode;
    }

    /**
     * Render an exception to the console
     *
     * {@inheritDoc}
     */
    public function renderException($exception, $output)
    {
        $output->writeln(sprintf('<exception>%s</exception>', $exception->getMessage()));
    }

    /**
     * Wrap the add method and do not register commands which are unsupported by
     * the current transport.
     *
     * {@inheritDoc}
     */
    public function add(Command $command)
    {
        if ($command instanceof PhpcrShellCommand) {
            if ($this->showUnsupported || $command->isSupported($this->getHelperSet()->get('repository'))) {
                parent::add($command);
            }
        } else {
            parent::add($command);
        }
    }

    public function dispatchProfileInitEvent(InputInterface $sessionInput, OutputInterface $output)
    {
        $event = new Event\ProfileInitEvent($this->profile, $sessionInput, $output);
        $this->dispatcher->dispatch(PhpcrShellEvents::PROFILE_INIT, $event);
    }
}
