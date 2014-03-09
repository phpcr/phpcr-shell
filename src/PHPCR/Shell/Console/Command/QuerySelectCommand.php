<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuerySelectCommand extends Command
{
    protected function configure()
    {
        $this->setName('select');
        $this->setDescription('Execute an SQL query UNSTABLE');
        $this->addArgument('query');
        $this->setHelp(<<<EOT
This is an unstable feature, see notes for the <info>query</info> command.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = $input->getRawCommand();

        // trim ";" for people used to MysQL
        if (substr($sql, -1) == ';') {
            $sql = substr($sql, 0, -1);
        }

        $session = $this->getHelper('phpcr')->getSession();
        $qm = $session->getWorkspace()->getQueryManager();

        $query = $qm->createQuery($sql, 'JCR-SQL2');

        $start = microtime(true);
        $result = $query->execute();
        $elapsed = microtime(true) - $start;

        $this->getHelper('result_formatter')->format($result, $output, $elapsed);
    }
}
