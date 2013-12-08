<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
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
            $output->writeln(sprintf('%s ----', $i + 1));
            foreach ($selectorNames as $selectorName) {
                $node = $row->getNode($selectorName);
                $properties = $node->getProperties();
                $output->writeln(sprintf(
                    '  [%s] [path:<comment>%s</comment>] [uid:<info>%s</info>]',
                    $selectorName, $node->getPath(), $node->getIdentifier()
                ));

                foreach ($properties as $key => $value) {
                    $output->writeln(sprintf('    <info>%s</info> <fg=magenta>%s%s</fg=magenta>: %s',
                        $key,
                        PropertyType::nameFromValue($value->getType()),
                        $value->isMultiple() ? '[]' : '',
                        $this->formatValue($value)
                    ));
                }
            }
        }

        $output->writeln('');
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

            foreach ($array->getValue() as $value) {
                if ($value instanceof NodeInterface) {
                    $values[] = $value->getPath();
                } else if (is_object($value)) {
                    $values[] = '<UNKNOWN OBJECT>';
                } else {
                    $values[] = $value;
                }
            }

            return "\n     - " . implode("\n     - ", $values);
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
