<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use PHPCR\Shell\Console\Command\Phpcr as CommandPhpcr;
use PHPCR\Shell\Console\Command\Shell as CommandShell;
use PHPCR\Shell\Event;
use PHPCR\Shell\Event\ApplicationInitEvent;
use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\PhpcrShell;
use PHPCR\Shell\Console\Command\Phpcr\BasePhpcrCommand;

/**
 * Main application for PHPCRSH
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ShellApplication extends Application
{
    /**
     * @var boolean
     */
    protected $showUnsupported = false;

    /**
     * @var Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * @var boolean
     */
    protected $debug = false;

    /**
     * @var boolean
     */
    protected $initialized = false;

    /**
     * Constructor - name and version inherited from SessionApplication
     *
     * {@inheritDoc}
     */
    public function __construct($container)
    {
        parent::__construct(PhpcrShell::APP_NAME, PhpcrShell::APP_VERSION);
        $this->dispatcher = $container->get('event.dispatcher') ?: new EventDispatcher();
        $this->setDispatcher($this->dispatcher);
        $this->container = $container;
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
     * Initialize the application
     */
    public function init()
    {
        if (true === $this->initialized) {
            return;
        }

        $this->registerPhpcrCommands();
        $this->registerPhpcrStandaloneCommands();
        $this->registerShellCommands();

        $event = new ApplicationInitEvent($this);
        $this->dispatcher->dispatch(PhpcrShellEvents::APPLICATION_INIT, $event);
        $this->initialized = true;
    }

    /**
     * Register the commands used in the shell
     */
    protected function registerPhpcrCommands()
    {
        // phpcr commands
        $this->add(new CommandPhpcr\AccessControlPrivilegeListCommand());
        $this->add(new CommandPhpcr\RepositoryDescriptorListCommand());
        $this->add(new CommandPhpcr\SessionExportCommand());
        $this->add(new CommandPhpcr\SessionImpersonateCommand());
        $this->add(new CommandPhpcr\SessionImportCommand());
        $this->add(new CommandPhpcr\SessionInfoCommand());
        $this->add(new CommandPhpcr\SessionNamespaceListCommand());
        $this->add(new CommandPhpcr\SessionNamespaceSetCommand());
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
        $this->add(new CommandPhpcr\NodeCloneCommand());
        $this->add(new CommandPhpcr\NodeCopyCommand());
        $this->add(new CommandPhpcr\NodeEditCommand());
        $this->add(new CommandPhpcr\WorkspaceNamespaceListCommand());
        $this->add(new CommandPhpcr\WorkspaceNamespaceRegisterCommand());
        $this->add(new CommandPhpcr\WorkspaceNamespaceUnregisterCommand());
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
        $this->add(new CommandPhpcr\NodeRemoveCommand());
        $this->add(new CommandPhpcr\LockLockCommand());
        $this->add(new CommandPhpcr\LockInfoCommand());
        $this->add(new CommandPhpcr\LockRefreshCommand());
        $this->add(new CommandPhpcr\LockTokenAddCommand());
        $this->add(new CommandPhpcr\LockTokenListCommand());
        $this->add(new CommandPhpcr\LockTokenRemoveCommand());
        $this->add(new CommandPhpcr\LockUnlockCommand());
    }

    protected function registerPhpcrStandaloneCommands()
    {
        $this->add(new CommandPhpcr\SessionLoginCommand());
        $this->add(new CommandPhpcr\SessionLogoutCommand());
        $this->add(new CommandPhpcr\WorkspaceUseCommand());
        $this->add(new CommandPhpcr\WorkspaceCreateCommand());
        $this->add(new CommandPhpcr\WorkspaceDeleteCommand());
        $this->add(new CommandPhpcr\WorkspaceListCommand());
    }

    protected function registerShellCommands()
    {
        // add shell-specific commands
        $this->add(new CommandShell\AliasListCommand());
        $this->add(new CommandShell\ClearCommand());
        $this->add(new CommandShell\ConfigInitCommand());
        $this->add(new CommandShell\ConfigReloadCommand());
        $this->add(new CommandShell\PathChangeCommand());
        $this->add(new CommandShell\PathShowCommand());
        $this->add(new CommandShell\ExitCommand());
    }

    /**
     * Configure the output formatter
     */
    private function configureFormatter(OutputFormatter $formatter)
    {
        $style = new OutputFormatterStyle('yellow', null, array('bold'));
        $formatter->setStyle('path', $style);

        $style = new OutputFormatterStyle('green');
        $formatter->setStyle('localname', $style);

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
            $input = new ArrayInput(array('command' => $this->getDefaultCommand()));
        }

        try {
            $exitCode = parent::doRun($input, $output);
        } catch (\Exception $e) {
            $this->dispatcher->dispatch(PhpcrShellEvents::COMMAND_EXCEPTION, new Event\CommandExceptionEvent($e, $this, $output));

            return 1;
        }

        return $exitCode;
    }

    /**
     * Return the default command
     */
    protected function getDefaultCommand()
    {
        return 'shell:path:show';
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
        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }

        if ($command instanceof BasePhpcrCommand) {
            if ($this->showUnsupported || $command->isSupported()) {
                parent::add($command);
            }
        } else {
            parent::add($command);
        }
    }

    public function dispatchProfileInitEvent(InputInterface $sessionInput, OutputInterface $output)
    {
        $event = new Event\ProfileInitEvent($this->container->get('config.profile'), $sessionInput, $output);
        $this->dispatcher->dispatch(PhpcrShellEvents::PROFILE_INIT, $event);
    }

    /**
     * Autocomplete invokes this method to get the command name completiongs.
     * If autocomplete is invoked before a command has been run, then
     * we need to initialize the application (and register the commands).
     *
     * {@inheritDoc}
     */
    public function all($namespace = null)
    {
        $this->init();
        return parent::all($namespace);
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Return if the shell is in debug mode
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Debug mode -- more verbose exceptions
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
}
