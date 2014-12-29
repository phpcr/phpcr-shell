<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Application\ShellApplication;
use PHPCR\Shell\Console\Application\Shell;
use PHPCR\Shell\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputArgument;

/**
 * The shell command is the command used to configure the shell session
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ShellCommand extends Command
{
    /**
     * @var ShellApplication
     */
    protected $application;

    /**
     * Constructor - construct with the shell application. This
     * command provides the connection parameters (by simply passing
     * the Input object).
     *
     * @param ShellApplication $application
     */
    public function __construct(ShellApplication $application)
    {
        $this->application = $application;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('phpcr_shell');
        $this->setDefinition(array(
            new InputOption('--help',           '-h',    InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--verbose',        '-v',    InputOption::VALUE_NONE, 'Increase verbosity of messages.'),
            new InputOption('--version',        '-V',    InputOption::VALUE_NONE, 'Display this application version.'),
            new InputOption('--ansi',           '',      InputOption::VALUE_NONE, 'Force ANSI output.'),
            new InputOption('--no-ansi',        '',      InputOption::VALUE_NONE, 'Disable ANSI output.'),

            new InputOption('--transport',      '-t',    InputOption::VALUE_REQUIRED, 'Transport to use.'),
            new InputOption('--phpcr-username', '-pu',   InputOption::VALUE_REQUIRED, 'PHPCR Username.', 'admin'),
            new InputOption('--phpcr-password', '-pp',   InputOption::VALUE_OPTIONAL, 'PHPCR Password.', 'admin'),
            new InputOption('--db-username',    '-du',   InputOption::VALUE_REQUIRED, 'Database Username.', 'root'),
            new InputOption('--db-name',        '-dn',   InputOption::VALUE_REQUIRED, 'Database Name.', 'phpcr'),
            new InputOption('--db-password',    '-dp',   InputOption::VALUE_OPTIONAL, 'Database Password.'),
            new InputOption('--db-host',        '-dh',   InputOption::VALUE_REQUIRED, 'Database Host.', 'localhost'),
            new InputOption('--db-driver',      '-dd',   InputOption::VALUE_REQUIRED, 'Database Transport.', 'pdo_mysql'),
            new InputOption('--db-path',        '-dP',   InputOption::VALUE_REQUIRED, 'Database Path.'),
            new InputOption('--no-interaction', null,    InputOption::VALUE_NONE, 'Turn off interaction (for testing purposes)'),
            new InputOption('--repo-url',       '-url',  InputOption::VALUE_REQUIRED, 'URL of repository (e.g. for jackrabbit).', 'http://localhost:8080/server/'),
            new InputOption('--repo-path',       '-path', InputOption::VALUE_REQUIRED, 'Path to repository (e.g. for Jackalope FS).', '/home/myuser/www/myproject/app/data'),

            new InputOption('--profile',      '-p',    InputOption::VALUE_OPTIONAL, 'Speicfy a profile name, use wit <info>--transport</info> to update or create'),
            new InputOption('--unsupported',    null,    InputOption::VALUE_NONE, 'Show all commands, including commands not supported by the repository'),
            new InputOption('--command',        null,    InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Run the given command'),

            new InputArgument('workspace', InputArgument::OPTIONAL, 'Workspace to start with', 'default'),
    ));
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $showUnspported = $input->getOption('unsupported');

        $application = $this->application;
        $application->setShowUnsupported($showUnspported);
        $application->dispatchProfileInitEvent($input, $output);

        if ($input->getOption('verbose')) {
            $application->setDebug(true);
        }

        $noInteraction = $input->getOption('no-interaction');

        if ($commands = $input->getOption('command')) {
            $application->setCatchExceptions(false);
            $application->setAutoExit(false);

            foreach ($commands as $command) {
                $input = new StringInput($command);
                $application->run($input, $output);
            }

            return;
        } else {
            $application = new Shell($this->application);
        }

        if ($noInteraction) {
            return 0;
        }

        $application->run($output);
    }
}
