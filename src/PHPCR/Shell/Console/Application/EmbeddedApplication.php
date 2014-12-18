<?php

namespace PHPCR\Shell\Console\Application;

use PHPCR\Shell\DependencyInjection\Container;
use PHPCR\Shell\Console\Helper\PhpcrHelper;

/**
 * Subclass of the full ShellApplication for running as an EmbeddedApplication
 * (e.g. from with the DoctrinePhpcrBundle)
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EmbeddedApplication extends ShellApplication
{
    /**
     * @deprecated remove after DoctrinePhpcrBundle is upgraded
     */
    const MODE_COMMAND = Container::MODE_EMBEDDED_COMMAND;

    /**
     * @deprecated remove after DoctrinePhpcrBundle is upgraded
     */
    const MODE_SHELL = Container::MODE_EMBEDDED_SHELL;

    protected $mode;

    /**
     * The $mode can be one of EmbeddedApplication::MODE_SHELL or EmbeddedApplication::MODE_COMMAND.
     *
     * - Shell mode initializes the whole environement
     * - Command mode initailizes only enough to run commands
     *
     * @param string $mode
     */
    public function __construct($mode)
    {
        $this->mode = $mode;
        $container = new Container($this->mode);
        parent::__construct($container, SessionApplication::APP_NAME, SessionApplication::APP_VERSION);
        $this->setAutoExit(false);

        // @deprecated This will be removed in 1.0
        $this->getHelperSet()->set(new PhpcrHelper($container->get('phpcr.session_manager')));
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if (true === $this->initialized) {
            return;
        }

        $this->registerPhpcrCommands();

        if ($this->container->getMode() === self::MODE_SHELL) {
            $this->registerShellCommands();
        }

        $this->initialized = true;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultCommand()
    {
        return $this->mode === self::MODE_SHELL ? 'shell:path:show' : 'list';
    }
}
