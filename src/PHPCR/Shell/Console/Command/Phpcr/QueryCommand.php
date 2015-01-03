<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class QueryCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('query');
        $this->setDescription('Execute a SELECT query (advanced)');
        $this->addArgument('query', InputArgument::REQUIRED, 'Query to execute');
        $this->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'The query language (e.g. jcr-sql2', 'JCR-SQL2');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'The query limit', 0);
        $this->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'The query offset', 0);
        $this->setHelp(<<<EOT
Execute an SQL query. This command differs from <info>select</info> in that it
is executed conventionally and not literally. The advantage is that you can
specify a specific query language and additional options:

    query "SELECT * FROM [nt:unstructured]" --language=JCR_SQL2 --limit=5 --offset=4
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $language = $input->getOption('language');
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');
        $query = $input->getArgument('query');

        $session = $this->get('phpcr.session');
        $workspace = $session->getWorkspace();
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

        $this->get('helper.result_formatter')->formatQueryResult($result, $output, $elapsed);
    }
}
