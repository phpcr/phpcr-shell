<?php

namespace PHPCR\Shell\Query;

use PHPCR\Shell\Query\FunctionOperand;
use PHPCR\NodeInterface;
use PHPCR\Query\InvalidQueryException;
use PHPCR\Query\RowInterface;

/**
 * Processor for node updates
 */
class UpdateProcessor
{
    /**
     * Functions available when calling SET
     * 
     * @var \Closure[]
     */
    private $functionMap = array();

    public function __construct()
    {
        $this->functionMap = array(
            'array_replace' => function ($operand, $v, $x, $y) {
                $operand->validateScalarArray($v);
                foreach ($v as $key => $value) {
                    if ($value === $x) {
                        $v[$key] = $y;
                    }
                }

                return $v;
            },
            'array_remove' => function ($operand, $v, $x) {
                foreach ($v as $key => $value) {
                    if ($value === $x) {
                        unset($v[$key]);
                    }
                }

                return array_values($v);
            },
            'array_append' => function ($operand, $v, $x) {
                $operand->validateScalarArray($v);
                $v[] = $x;
                return $v;
            },
            'array' => function () {
                $values = func_get_args();

                // first argument is the operand
                array_shift($values);
                return $values;
            },
            'array_replace_at' => function ($operand, $current, $index, $value) {
                if (!isset($current[$index])) {
                    throw new \InvalidArgumentException(sprintf(
                        'Multivalue index "%s" does not exist',
                        $index
                    ));
                }

                if (null !== $value && !is_scalar($value)) {
                    throw new \InvalidArgumentException('Cannot use an array as a value in a multivalue property');
                }

                if (null === $value) {
                    unset($current[$index]);
                } else {
                    $current[$index] = $value;
                }

                return array_values($current);
            },
        );
    }

    /**
     * Update a node indicated in $propertyData in $row
     *
     * @param PHPCR\Query\RowInterface
     * @param array
     */
    public function updateNode(RowInterface $row, $propertyData)
    {
        $node = $row->getNode($propertyData['selector']);

        if ($node->hasProperty($propertyData['name'])) {
            $value = $this->handleExisting($row, $node, $propertyData);
        } else {
            $value = $propertyData['value'];
        }

        $node->setProperty($propertyData['name'], $value);
    }

    private function handleExisting($row, $node, $propertyData)
    {
        $phpcrProperty = $node->getProperty($propertyData['name']);
        $value = $propertyData['value'];

        if ($value instanceof FunctionOperand) {
            return $this->handleFunction($row, $node, $phpcrProperty, $propertyData);
        }

        return $value;
    }

    private function handleFunction($row, $node, $phpcrProperty, $propertyData)
    {
        $currentValue = $phpcrProperty->getValue();
        $value = $propertyData['value'];

        $value = $value->execute($this->functionMap, $row);

        if ($phpcrProperty->isMultiple()) {
            // do not allow updating multivalue with scalar
            if (false === is_array($value) && sizeof($currentValue) > 1) {
                throw new \InvalidArgumentException(sprintf(
                    '<error>Cannot update multivalue property "%s" with a scalar value.</error>',
                    $phpcrProperty->getName()
                ));
            }
        }

        return $value;
    }
}
