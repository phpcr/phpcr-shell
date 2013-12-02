<?php

namespace PHPCR\Shell\Console\Command\Query;

use PHPCR\Shell\Console\ShellQueryCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Shell\Console\Command\AbstractSessionCommand;

class SelectCommand extends AbstractSessionCommand
{
    protected function configure()
    {
        $this->setName('select');
        $this->setDescription('Execute an JCR_SQL2 query.');
        $this->addArgument('query');
        $this->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'The query language (e.g. jcr-sql2', 'jcr-sql2');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'The query limit', 0);
        $this->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'The query offset', 0);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = $input->getRawCommand();

        // trim ";" for people used to MysQL
        if (substr($sql, -1) == ';') {
            $sql = substr($sql, 0, -1);
        }

        $language = strtoupper($input->getOption('language'));
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');

        $session = $this->getSession();
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
