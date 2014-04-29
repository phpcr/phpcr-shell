<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QuerySelectCommand extends Command
{
    protected function configure()
    {
        $this->setName('select');
        $this->setDescription('Execute an JCR-SQL2 query');
        $this->addArgument('query');
        $this->setHelp(<<<EOT
Execute a JCR-SQL2 query. Unlike other commands you can enter a query literally:

     SELECT * FROM [nt:unstructured];

This command only executes JCR-SQL2 queries at the moment.
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

        $this->getHelper('result_formatter')->formatQueryResult($result, $output, $elapsed);
    }
}
