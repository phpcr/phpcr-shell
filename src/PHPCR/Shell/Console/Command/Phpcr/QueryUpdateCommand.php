<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Query\UpdateParser;

class QueryUpdateCommand extends Command
{
    protected function configure()
    {
        $this->setName('update');
        $this->setDescription('Execute an UPDATE JCR-SQL2 query');
        $this->addArgument('query');
        $this->setHelp(<<<EOT
Execute a JCR-SQL2 update query. Unlike other commands you can enter a query literally:

     UPDATE [nt:unstructured] AS a SET title = 'foobar' WHERE a.title = 'barfoo';

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

        $session = $this->getHelper('phpcr')->getSession();
        $qm = $session->getWorkspace()->getQueryManager();

        $updateParser = new UpdateParser($qm->getQOMFactory());
        $res = $updateParser->parse($sql);
        $query = $res->offsetGet(0);
        $updates = $res->offsetGet(1);

        $start = microtime(true);
        $result = $query->execute();
        $rows = 0;

        foreach ($result as $row) {
            $rows++;
            foreach ($updates as $field => $property) {
                $node = $row->getNode($property['selector']);

                if ($node->hasProperty($property['name'])) {
                    $phpcrProperty = $node->getProperty($property['name']);

                    if ($phpcrProperty->isMultiple()) {
                        $currentValue = $phpcrProperty->getValue();

                        if (sizeof($currentValue) > 1) {
                            $output->writeln(sprintf(
                                '<error>Cannot update property "%s". Updating multi-value nodes with more than one element not currently supported</error>',
                                $phpcrProperty->getName()
                            ));
                            $output->writeln(sprintf(
                                '<error>See: https://github.com/phpcr/phpcr-shell/issues/81</error>',
                                $phpcrProperty->getName()
                            ));
                        }

                        $property['value'] = (array) $property['value'];
                    }
                }

                $node->setProperty($property['name'], $property['value']);
            }
        }

        $elapsed = microtime(true) - $start;

        $output->writeln(sprintf('%s row(s) affected in %ss', $rows, number_format($elapsed, 2)));
    }
}
