<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\TableHelper;
use PHPCR\Query\QueryResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\PropertyType;
use PHPCR\NodeInterface;

class ResultFormatterHelper extends Helper
{
    public function getName()
    {
        return 'result_formatter';
    }

    public function format(QueryResultInterface $result, OutputInterface $output, $elapsed)
    {
        $selectorNames = $result->getSelectorNames();

        foreach ($result->getRows() as $i => $row) {
            $output->writeln(sprintf($str = '| <info>Row:</info> #%d <info>Score:</info> %d',
                $i, $row->getScore()
            ));

            foreach ($selectorNames as $selectorName) {
                $node = $row->getNode($selectorName);
                $properties = $node->getProperties();
                $output->writeln(sprintf('| <info>selector:</info> %s <info>path:</info> %s <info>uid:</info> %s',
                    $selectorName, $node->getPath(), $node->getIdentifier()
                ));

                $table = new TableHelper;
                $table->setHeaders(array('Name', 'Type', 'Multiple', 'Value'));
                foreach ($properties as $key => $value) {
                    $table->addRow(array(
                        $key,
                        PropertyType::nameFromValue($value->getType()),
                        $value->isMultiple() ? 'yes' : 'no',
                        $this->formatValue($value)
                    ));
                }
                $table->render($output);
            }
            $output->writeln('');
        }

        $output->writeln(sprintf('%s rows in set (%s sec)', count($result->getRows()), number_format($elapsed, 2)));
    }

    protected function formatValue($value)
    {
        if (is_array($value->getValue())) {
            if (empty($value->getValue())) {
                return '';
            }
            $array = $value;
            $values = array();

            foreach ($array->getValue() as $i => $value) {
                if ($value instanceof NodeInterface) {
                    $value = $value->getPath();
                } else if (is_object($value)) {
                    $value = '<UNKNOWN OBJECT>';
                } else {
                    $value = $value;
                }
                $value = '[' . $i . '] ' . $value;
                $values[] = $value;
            }

            return implode("\n", $values);
        }

        switch (intval($value->getType())) {
            case PropertyType::UNDEFINED :
                return '#UNDEFINED#';
            case PropertyType::BINARY :
                return '(binary data)';
            case PropertyType::BOOLEAN :
                return $value->getValue() ? 'true' : 'false';
            case PropertyType::DATE :
                return $value->getValue()->format('c');
            case PropertyType::REFERENCE :
            case PropertyType::WEAKREFERENCE :
                return $value->getValue()->getPath();
            case PropertyType::URI :
            case PropertyType::STRING :
            case PropertyType::NAME :
            case PropertyType::LONG :
            case PropertyType::DOUBLE :
            case PropertyType::DECIMAL :
            case PropertyType::PATH :
                return $value->getValue();
            default:
                throw new \RuntimeException('Unknown type ' . $value->getType());
        }
    }
}
