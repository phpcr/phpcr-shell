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
use PHPCR\Shell\Query\UpdateParser;
use PHPCR\Shell\Query\UpdateProcessor;

class QueryUpdateCommand extends BasePhpcrCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this->setName('update');
        $this->setDescription('Execute an UPDATE query (non-standard)');
        $this->addArgument('query');
        $this->setHelp(<<<EOT
Execute a PHPCR-Shell JCR-SQL2 update query. You can enter a query literally:

     UPDATE [nt:unstructured] AS a SET title = 'foobar' WHERE a.title = 'barfoo';

You can also manipulate multivalue fields:

     # Delete index


And you have access to a set of functions when assigning a value:

     # Delete a multivalue index
     UPDATE [nt:unstructured] SET a.tags = array_set(a.tags, 0, NULL)

     # Set a multivalue index
     UPDATE [nt:unstructured] SET a.tags = array_set(a.tags, 0, 'foo')

     # Replace the multivalue value "Planes" with "Trains"
     UPDATE [nt:unstructured] AS a SET a.tags[] = array_replace(a.tags, 'Planes', 'Trains')

     # Append a multivalue
     UPDATE [nt:unstructured] AS a SET a.tags = array_append(a.tags, 'Rockets')

     # Remove by value
     UPDATE [nt:unstructured] AS a SET a.tags = array_remove(a.tags, 'Plains')

Refer to the documentation for a full reference: http://phpcr.readthedocs.org/en/latest/phpcr-shell

You must call <info>session:save</info> to persist changes.

Note that this command is not part of the JCR-SQL2 language but is implemented specifically
for the PHPCR-Shell.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $sql = $input->getRawCommand();

        // trim ";" for people used to MysQL
        if (substr($sql, -1) == ';') {
            $sql = substr($sql, 0, -1);
        }

        $session = $this->get('phpcr.session');
        $qm = $session->getWorkspace()->getQueryManager();

        $updateParser = new UpdateParser($qm->getQOMFactory());
        $res = $updateParser->parse($sql);
        $query = $res->offsetGet(0);
        $updates = $res->offsetGet(1);
        $applies = $res->offsetGet(3);


        $start = microtime(true);
        $result = $query->execute();
        $rows = 0;

        $updateProcessor = new UpdateProcessor();

        foreach ($result as $row) {
            $rows++;
            foreach ($updates as $property) {
                $updateProcessor->updateNodeSet($row, $property);
            }

            foreach ($applies as $apply) {
                $updateProcessor->updateNodeApply($row, $apply);
            }
        }

        $elapsed = microtime(true) - $start;

        $output->writeln(sprintf('%s row(s) affected in %ss', $rows, number_format($elapsed, 2)));
    }
}
