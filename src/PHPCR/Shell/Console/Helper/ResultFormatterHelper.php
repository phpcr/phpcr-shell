<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use PHPCR\Query\QueryResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\PropertyType;
use PHPCR\NodeInterface;
use PHPCR\PropertyInterface;
use PHPCR\Shell\Config\Config;

/**
 * Provide methods for formatting PHPCR objects
 *
 * @TODO: Rename this to PhpcrFormatterHelper
 */
class ResultFormatterHelper extends Helper
{
    protected $textHelper;
    protected $tableHelper;
    protected $config;

    public function __construct(TextHelper $textHelper, TableHelper $tableHelper, Config $config)
    {
        $this->textHelper = $textHelper;
        $this->tableHelper = $tableHelper;
        $this->config = $config;
    }

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
        $table = $this->tableHelper->create();
        $table->setHeaders(array_merge(array(
            'Path',
            'Index',
        ), $result->getColumnNames()));

        foreach ($result->getRows() as $row) {
            $values = array_merge(array(
                $row->getPath(),
                $row->getNode()->getIndex(),
            ), $row->getValues());

            foreach ($values as &$value) {
                $value = $this->normalizeValue($value);
            }

            $table->addRow($values);
        }

        $table->render($output);

        if (true === $this->config['show_execution_time_query']) {
            $output->writeln(sprintf(
                '%s rows in set (%s sec)',
                count($result->getRows()),
                number_format($elapsed, $this->config['execution_time_expansion']))
            );
        }
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
                    $uuid = $value->getIdentifier();
                    $value = $value->getPath();
                    if ($uuid) {
                        $value .= ' (' . $uuid . ')';
                    }
                } elseif (is_object($value)) {
                    $value = '<UNKNOWN OBJECT>';
                } else {
                    $value = $value;
                }
                $value = '[' . $i . '] ' . $this->textHelper->truncate($value, 255);
                $values[] = $value;
            }

            return implode("\n", $values);
        }

        if ($value instanceof \DateTime) {
            return $value->format('c');
        }

        return $this->textHelper->truncate($value);
    }

    public function formatValue(PropertyInterface $value, $showBinary = false)
    {
        if (is_array($value->getValue())) {
            return $this->normalizeValue($value->getValue());
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
                return $this->textHelper->truncate($value->getValue());
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
