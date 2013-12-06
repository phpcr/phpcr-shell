<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Application\ShellApplication;
use PHPCR\Shell\Console\Application\Shell;
use PHPCR\SimpleCredentials;
use PHPCR\Shell\PhpcrSession;

class ShellCommand extends Command
{
    protected $output;

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
    ));
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $transport = $this->getTransport($input);
        $repository = $transport->getRepository();

        $credentials = new SimpleCredentials(
            $input->getOption('phpcr-username'),
            $input->getOption('phpcr-password')
        );

        $session = $repository->login($credentials);
        $session = new PhpcrSession($session);

        $application = new Shell(new ShellApplication($session));
        $application->run($input, $output);
    }

    protected function getTransport(InputInterface $input)
    {
        foreach (array(
            new \PHPCR\Shell\Transport\DoctrineDbal($input),
            new \PHPCR\Shell\Transport\Jackrabbit($input),
        ) as $transport) {
            $transports[$transport->getName()] = $transport;
        }

        $transportName = $input->getOption('transport');

        if (!isset($transports[$transportName])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown transport "%s", I have "%s"',
                $transportName, implode(', ', array_keys($transports))
            ));
        }

        $transport = $transports[$transportName];

        return $transport;
    }
}
