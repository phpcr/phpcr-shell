<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use PHPCR\Query\QueryResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\PropertyType;

class ResultFormatterHelper extends Helper
{
    public function getName()
    {
        return 'result_formatter';
    }

    public function format(QueryResultInterface $result, OutputInterface $output)
    {
        $selectorNames = $result->getSelectorNames();
        foreach ($result->getRows() as $i => $row) {
            $output->writeln($i);
            foreach ($selectorNames as $selectorName) {
                $node = $row->getNode($selectorName);
                $properties = $node->getProperties();
                $output->writeln('  ' . $selectorName);
                $output->writeln('    <comment>' . $node->getPath().'</comment>');

                foreach ($properties as $key => $value) {
                    $output->writeln(sprintf('    <info>%s</info> %s%s: %s',
                        $key,
                        PropertyType::nameFromValue($value->getType()),
                        $value->isMultiple() ? '[]' : '',
                        $this->formatValue($value)
                    ));
                }
            }
        }
    }

    protected function formatValue($value)
    {
        if (is_array($value->getValue())) {
            if (empty($value->getValue())) {
                return '';
            }

            return "\n     - " . implode("\n     - ", $value->getvalue());
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
