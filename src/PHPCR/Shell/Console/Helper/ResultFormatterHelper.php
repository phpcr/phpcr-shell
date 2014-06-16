<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\TableHelper;
use PHPCR\Query\QueryResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\PropertyType;
use PHPCR\NodeInterface;
use PHPCR\PropertyInterface;

/**
 * Provide methods for formatting PHPCR objects
 *
 * @TODO: Rename this to PhpcrFormatterHelper
 */
class ResultFormatterHelper extends Helper
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'result_formatter';
    }

    /**
     * Return the name of a property from its enumeration (i.e.
     * the value of its CONSTANT)
     *
     * @return string
     */
    public function getPropertyTypeName($typeInteger)
    {
        $refl = new \ReflectionClass('PHPCR\PropertyType');
        foreach ($refl->getConstants() as $key => $value) {
            if ($typeInteger == $value) {
                return $key;
            }
        }
    }

    /**
     * Render a table with the results of the given QueryResultInterface
     */
    public function formatQueryResult(QueryResultInterface $result, OutputInterface $output, $elapsed)
    {
        $selectorNames = $result->getSelectorNames();

        $table = new TableHelper;
        $table->setHeaders($result->getColumnNames());

        foreach ($result->getRows() as $i => $row) {
            $values = $row->getValues();

            foreach ($values as $columnName => &$value) {
                $value = $this->normalizeValue($value);
            }

            $table->addRow($values);
        }

        $table->render($output);
        $output->writeln(sprintf('%s rows in set (%s sec)', count($result->getRows()), number_format($elapsed, 2)));
    }

    public function normalizeValue($value)
    {
        if (is_array($value)) {
            if (empty($value)) {
                return '';
            }
            $array = $value;
            $values = array();

            foreach ($array as $i => $value) {
                if ($value instanceof NodeInterface) {
                    $value = $value->getPath();
                } elseif (is_object($value)) {
                    $value = '<UNKNOWN OBJECT>';
                } else {
                    $value = $value;
                }
                $value = '[' . $i . '] ' . $value;
                $values[] = $value;
            }

            return implode("\n", $values);
        }

        if ($value instanceof \DateTime) {
            return $value->format('c');
        }

        return $value;
    }

    public function formatValue(PropertyInterface $value, $showBinary = false)
    {
        $v = $value->getValue();

        if (is_array($v)) {
            return $this->normalizeValue($v);
        }

        switch (intval($value->getType())) {
            case PropertyType::UNDEFINED :
                return '#UNDEFINED#';
            case PropertyType::BINARY :
                if ($showBinary) {
                    $lines = array();
                    $pointer = $value->getValue();
                    while (($line = fgets($pointer)) !== false) {
                        $lines[] = $line;
                    }

                    return implode('', $lines);
                }

                return '(binary data)';
            case PropertyType::BOOLEAN :
                return $value->getValue() ? 'true' : 'false';
            case PropertyType::DATE :
                return $value->getValue()->format('c');
            case PropertyType::REFERENCE :
            case PropertyType::WEAKREFERENCE :
                return $value->getValue()->getIdentifier();
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

    public function formatNodePropertiesInline(NodeInterface $node)
    {
        $out = array();

        foreach ($node->getProperties() as $property) {
            $out[] = sprintf('%s: %s',
                $property->getName(),
                $this->formatValue($property)
            );
        }

        return implode(', ', $out);
    }

    public function formatNodeName(NodeInterface $node)
    {
        return sprintf('%s%s', $node->getName(), $node->hasNodes() ? '/' : '');
    }

    public function formatException(\Exception $e)
    {
        if ($e instanceof \Jackalope\NotImplementedException) {
            return '[ERROR] Not implemented by jackalope';
        }

        return sprintf('[%s] %s', get_class($e), $e->getMessage());
    }
}
