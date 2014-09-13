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
    protected $functionMap = array();

    public function __construct()
    {
        $this->functionMap = array(
            'array_replace' => function ($v, $x, $y) {
                foreach ($v as $key => $value) {
                    if ($value === $x) {
                        $v[$key] = $y;
                    }
                }

                return $v;
            },
            'array_remove' => function ($v, $x) {
                foreach ($v as $key => $value) {
                    if ($value === $x) {
                        unset($v[$key]);
                    }
                }

                return array_values($v);
            },
            'array_append' => function ($v, $x) {
                $v[] = $x;
                return $v;
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

    protected function handleExisting($row, $node, $propertyData)
    {
        $phpcrProperty = $node->getProperty($propertyData['name']);
        $value = $propertyData['value'];

        if ($phpcrProperty->isMultiple()) {
            return $this->handleMultiValue($row, $node, $phpcrProperty, $propertyData);
        }

        return $value;
    }

    protected function handleMultiValue($row, $node, $phpcrProperty, $propertyData)
    {
        $currentValue = $phpcrProperty->getValue();
        $value = $propertyData['value'];

        // there is an array operator ([] or [x])
        if (isset($propertyData['array_op'])) {
            $arrayOp = $propertyData['array_op'];
            if ($arrayOp === UpdateParser::ARRAY_OPERATION_ADD) {
                foreach ((array) $value as $v) {
                    $currentValue[] = $v;
                }

                return $currentValue;
            } elseif ($arrayOp === UpdateParser::ARRAY_OPERATION_SUB) {
                $arrayIndex = $propertyData['array_index'];

                if (!isset($currentValue[$arrayIndex])) {
                    throw new \InvalidArgumentException(sprintf(
                        'Multivalue index "%s" does not exist in multivalue field "%s"', $arrayIndex, $propertyData['name']
                    ));
                }

                if (null === $value) {
                    unset($currentValue[$arrayIndex]);
                    return array_values($currentValue);
                }

                if (is_array($value)) {
                    throw new \InvalidArgumentException(sprintf('Cannot set index to array value on "%s"', $propertyData['name']));
                }

                $currentValue[$arrayIndex] = $value;

                return $currentValue;
            }
        }

        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof FunctionOperand) {
            $value->replaceColumnOperands($row);
            $functionName = $value->getFunctionName();
            if (!isset($this->functionMap[$functionName])) {
                throw new InvalidQueryException(sprintf('Unknown function "%s", known functions are "%s"',
                    $functionName,
                    implode(', ', array_keys($this->functionMap))
                ));
            }

            $callable = $this->functionMap[$functionName];
            $value = call_user_func_array($callable, $value->getArguments());
        }

        // do not allow updating multivalue with scalar
        if (false === is_array($value) && sizeof($currentValue) > 1) {
            throw new \InvalidArgumentException(sprintf(
                '<error>Cannot update multivalue property "%s" with a scalar value.</error>',
                $phpcrProperty->getName()
            ));
        }

        return $value;
    }
}
