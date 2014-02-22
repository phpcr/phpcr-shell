<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueryCommand extends Command
{
    protected function configure()
    {
        $this->setName('query');
        $this->setDescription('Execute an SQL query.');
        $this->addArgument('query');
        $this->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'The query language (e.g. jcr-sql2', 'jcr-sql2');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'The query limit', 0);
        $this->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'The query offset', 0);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $language = strtoupper($input->getOption('language'));
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');

        $session = $this->getHelper('phpcr')->getSession();
        $qm = $session->getWorkspace()->getQueryManager();

        $query = $qm->createQuery($sql, $language);

        if ($limit) {
            $query->setLimit($limit);
        }

        if ($offset) {
            $query->setOffset($offset);
        }

        $start = microtime(true);
        $result = $query->execute();
        $elapsed = microtime(true) - $start;

        $this->getHelper('result_formatter')->format($result, $output, $elapsed);
    }
}
