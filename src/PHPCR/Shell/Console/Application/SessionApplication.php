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

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use PHPCR\Shell\Console\Command\ShellCommand;
use PHPCR\Shell\DependencyInjection\Container;
use PHPCR\Shell\PhpcrShell;

/**
 * This application wraps a single command which accepts
 * the connection parameters and starts an interactive shell.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SessionApplication extends BaseApplication
{
    protected $shellApplication;

    /**
     * Constructor - add the single command ShellCommand which
     * accepts the connection parameters for the shell.
     */
    public function __construct()
    {
        parent::__construct(PhpcrShell::APP_NAME, PhpcrShell::APP_VERSION);

        $container = new Container();
        $this->shellApplication = $container->get('application');

        $command = new ShellCommand($this->shellApplication);
        $command->setApplication($this);
        $this->add($command);
    }

    public function getDefaultInputDefinition()
    {
        return new InputDefinition(array());
    }

    /**
     * This application always runs the phpcr_shell command to connect
     * to the shell.
     *
     * {@inheritDoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'phpcr_shell';
    }

    /**
     * Return the shell application
     *
     * @return ShellApplication
     */
    public function getShellApplication()
    {
        return $this->shellApplication;
    }
}
