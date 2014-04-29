<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueryCommand extends Command
{
    protected function configure()
    {
        $this->setName('query');
        $this->setDescription('Execute a query ');
        $this->addArgument('query');
        $this->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'The query language (e.g. jcr-sql2', 'JCR-SQL2');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'The query limit', 0);
        $this->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'The query offset', 0);
        $this->setHelp(<<<EOT
Execute an SQL query. This command differs from <info>select</info> in that it
is executed conventionally and not literally. The advantage is that you can
specify a specific query language and additional options:

    <info>query "SELECT * FROM [nt:unstructured]" --language=JCR_SQL2 --limit=5 --offset=4</info>
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $language = $input->getOption('language');
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');
        $query = $input->getArgument('query');

        $session = $this->getHelper('phpcr')->getSession();
        $workspace = $session->getWorkspace();;
        $supportedQueryLanguages = $workspace->getQueryManager()->getSupportedQueryLanguages();

        if (!in_array($language, $supportedQueryLanguages)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" is an invalid query language, valid query languages are:%s',
                $language,
                PHP_EOL . '    -' . implode(PHP_EOL . '   - ', $supportedQueryLanguages)
            ));
        }

        $qm = $session->getWorkspace()->getQueryManager();
        $query = $qm->createQuery($query, $language);

        if ($limit) {
            $query->setLimit($limit);
        }

        if ($offset) {
            $query->setOffset($offset);
        }

        $start = microtime(true);
        $result = $query->execute();
        $elapsed = microtime(true) - $start;

        $this->getHelper('result_formatter')->formatQueryResult($result, $output, $elapsed);
    }
}
