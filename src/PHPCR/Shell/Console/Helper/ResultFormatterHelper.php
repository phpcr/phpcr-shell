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
            ), $row->getValues());

            foreach ($values as &$value) {
                $value = $this->textHelper->truncate($value, 255);
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

    public function formatValue(PropertyInterface $property, $showBinary = false, $truncate = true)
    {
        $values = $property->getValue();
        if (false === $property->isMultiple()) {
            $values = array($values);
        }
        $return = array();

        foreach ($values as $value) {
            switch (intval($property->getType())) {
                case PropertyType::UNDEFINED :
                    $return[] = '#UNDEFINED#';
                case PropertyType::BINARY :
                    if ($showBinary) {
                        $lines = array();
                        $pointer = $value;
                        while (($line = fgets($pointer)) !== false) {
                            $lines[] = $line;
                        }

                        $return[] = implode('', $lines);
                        break;
                    }

                    return '(binary data)';
                case PropertyType::BOOLEAN :
                    $return[] = $value ? 'true' : 'false';
                    break;
                case PropertyType::DATE :
                    $return[] = $value->format('c');
                    break;
                case PropertyType::REFERENCE :
                case PropertyType::WEAKREFERENCE :
                    $return[] = sprintf(
                        '%s (%s)',
                        $this->textHelper->truncate($value->getPath(), 255),
                        $value->getIdentifier()
                    );
                    break;
                case PropertyType::URI :
                case PropertyType::STRING :
                    $return[] = $truncate ? $this->textHelper->truncate($value) : $value;
                    break;
                case PropertyType::NAME :
                case PropertyType::LONG :
                case PropertyType::DOUBLE :
                case PropertyType::DECIMAL :
                case PropertyType::PATH :
                    $return[] = $value;
                    break;
                default:
                    throw new \RuntimeException('Unknown type ' . $property->getType());
            }
        }

        if ($property->isMultiple()) {
            return implode("\n", array_map(function ($value) {
                static $index = 0;
                return sprintf('<comment>[%d]</comment> %s', $index++, $value);
            }, $return));
        }

        return implode("\n", $return);
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
