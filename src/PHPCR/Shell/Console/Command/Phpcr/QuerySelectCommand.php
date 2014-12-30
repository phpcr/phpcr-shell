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
use Symfony\Component\Console\Output\OutputInterface;

class QuerySelectCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('select');
        $this->setDescription('Execute a SELECT query (JCR-SQL2)');
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

        $session = $this->get('phpcr.session');
        $qm = $session->getWorkspace()->getQueryManager();

        $query = $qm->createQuery($sql, 'JCR-SQL2');

        $start = microtime(true);
        $result = $query->execute();
        $elapsed = microtime(true) - $start;

        $this->get('helper.result_formatter')->formatQueryResult($result, $output, $elapsed);
    }
}
