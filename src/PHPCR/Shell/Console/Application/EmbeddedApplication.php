<?php

namespace PHPCR\Shell\Console\Application;

use PHPCR\Shell\Subscriber;

/**
 * Subclass of the full ShellApplication for running as an EmbeddedApplication
 * (e.g. from with the DoctrinePhpcrBundle)
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EmbeddedApplication extends ShellApplication
{
    const MODE_SHELL = 'shell';
    const MODE_COMMAND = 'command';

    protected $mode = self::MODE_SHELL;

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
        parent::__construct(SessionApplication::APP_NAME, SessionApplication::APP_VERSION);
        $this->mode = $mode;
        $this->setAutoExit(false);
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

        if ($this->mode === self::MODE_SHELL) {
            $this->registerShellCommands();
        }

        $this->initialized = true;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultCommand()
    {
        return 'shell:path:show';
    }

    /**
     * {@inheritDoc}
     */
    protected function registerEventListeners()
    {
        $this->dispatcher->addSubscriber(new Subscriber\ProfileFromSessionInputSubscriber());
        $this->dispatcher->addSubscriber(new Subscriber\ExceptionSubscriber());
        $this->dispatcher->addSubscriber(new Subscriber\AliasSubscriber($this->getHelperSet()));
        $this->dispatcher->addSubscriber(new Subscriber\AutoSaveSubscriber());
    }
}
