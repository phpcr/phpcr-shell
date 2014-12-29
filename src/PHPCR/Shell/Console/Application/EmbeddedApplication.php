<?php

namespace PHPCR\Shell\Console\Application;

use PHPCR\Shell\DependencyInjection\Container;
use PHPCR\Shell\PhpcrShell;

/**
 * Subclass of the full ShellApplication for running as an EmbeddedApplication
 * (e.g. from with the DoctrinePhpcrBundle)
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EmbeddedApplication extends ShellApplication
{
    protected $mode;

    /**
     * The $mode can be one of PhpcrShell::MODE_SHELL or PhpcrShell::MODE_COMMAND.
     *
     * - Shell mode initializes the whole environement
     * - Command mode initailizes only enough to run commands
     *
     * @param string $mode
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->setAutoExit(false);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->registerPhpcrCommands();

        if ($this->container->getMode() === PhpcrShell::MODE_EMBEDDED_SHELL) {
            $this->registerShellCommands();
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultCommand()
    {
        return $this->mode === PhpcrShell::MODE_EMBEDDED_SHELL ? 'shell:path:show' : 'list';
    }
}
