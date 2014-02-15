<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Application\ShellApplication;
use PHPCR\Shell\Console\Application\Shell;
use Symfony\Component\Console\Input\StringInput;

class ShellCommand extends Command
{
    protected $output;
    protected $options;
    protected $application;

    public function __construct(ShellApplication $application)
    {
        $this->application = $application;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('phpcr_shell');
        $this->setDefinition(array(
            new InputOption('--help',           '-h',    InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--verbose',        '-v',    InputOption::VALUE_NONE, 'Increase verbosity of messages.'),
            new InputOption('--version',        '-V',    InputOption::VALUE_NONE, 'Display this application version.'),
            new InputOption('--ansi',           '',      InputOption::VALUE_NONE, 'Force ANSI output.'),
            new InputOption('--no-ansi',        '',      InputOption::VALUE_NONE, 'Disable ANSI output.'),
            new InputOption('--transport',      '-t',    InputOption::VALUE_REQUIRED, 'Transport to use.', 'doctrine-dbal'),
            new InputOption('--phpcr-username', '-pu',   InputOption::VALUE_REQUIRED, 'PHPCR Username.', 'admin'),
            new InputOption('--phpcr-password', '-pp',   InputOption::VALUE_OPTIONAL, 'PHPCR Password.', 'admin'),
            new InputOption('--phpcr-workspace','-pw',   InputOption::VALUE_OPTIONAL, 'PHPCR Workspace.', 'default'),
            new InputOption('--db-username',    '-du',   InputOption::VALUE_REQUIRED, 'Database Username.', 'root'),
            new InputOption('--db-name',        '-dn',   InputOption::VALUE_REQUIRED, 'Database Name.', 'phpcr'),
            new InputOption('--db-password',    '-dp',   InputOption::VALUE_OPTIONAL, 'Database Password.'),
            new InputOption('--db-host',        '-dh',   InputOption::VALUE_REQUIRED, 'Database Host.', 'localhost'),
            new InputOption('--db-driver',      '-dd',   InputOption::VALUE_REQUIRED, 'Database Transport.', 'pdo_mysql'),
            new InputOption('--db-path',        '-dP',   InputOption::VALUE_REQUIRED, 'Database Path.'),
            new InputOption('--repo-url',       '-url',  InputOption::VALUE_REQUIRED, 'URL of repository (e.g. for jackrabbit).',
                'http://localhost:8080/server/'
            ),
            new InputOption('--command',        null,    InputOption::VALUE_REQUIRED, 'Run the given command'),
    ));
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->application;
        $application->setSessionInput($input);

        if ($command = $input->getOption('command')) {
            $input = new StringInput($command);
        } else {
            $application = new Shell($this->application);
        }

        $application->run($input, $output);
    }
}
