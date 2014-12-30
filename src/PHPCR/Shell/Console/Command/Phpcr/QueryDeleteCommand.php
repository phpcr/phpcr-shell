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
use PHPCR\Util\QOM\Sql2ToQomQueryConverter;

class QueryDeleteCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('delete');
        $this->setDescription('Execute a DELETE query (non standard)');
        $this->addArgument('query');
        $this->setHelp(<<<EOT
Execute a DELETE query. Unlike other commands you can enter a query literally:

     DELETE FROM [nt:unstructured] WHERE title = 'foo';

You must call <info>session:save</info> to persist changes.

Note that this command is not part of the JCR-SQL2 language but is implemented specifically
for the PHPCR-Shell.
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

        if (!preg_match('{^delete from}', strtolower($sql))) {
            throw new \PHPCR\Query\InvalidQueryException(sprintf(
                '"FROM" not specified in DELETE query: "%s"', $sql
            ));
        }

        $sql = 'SELECT * FROM' . substr($sql, 11);

        $selectParser = new Sql2ToQomQueryConverter($qm->getQOMFactory());
        $query = $selectParser->parse($sql);

        $start = microtime(true);
        $result = $query->execute();
        $rows = 0;

        foreach ($result as $row) {
            $rows++;
            $node = $row->getNode();
            $node->remove();
        }

        $elapsed = microtime(true) - $start;
        $output->writeln(sprintf('%s row(s) affected in %ss', $rows, number_format($elapsed, 2)));
    }
}
