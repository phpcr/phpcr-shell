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

use PHPCR\Shell\DependencyInjection\Container;
use PHPCR\Shell\PhpcrShell;
use PHPCR\Shell\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Subclass of the full ShellApplication for running as an EmbeddedApplication
 * (e.g. from with the DoctrinePhpcrBundle)
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EmbeddedApplication extends ShellApplication
{
    /**
     * @param Container $container
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
        return $this->container->getMode() === PhpcrShell::MODE_EMBEDDED_SHELL ? 'shell:path:show' : 'list';
    }

    public function runWithStringInput($stringInput, OutputInterface $output)
    {
        $input = new StringInput($stringInput);
        $this->run($input, $output);
    }
}
