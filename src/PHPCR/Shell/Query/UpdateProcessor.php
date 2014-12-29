<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Query;

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
        $this->functionMapApply = array(
            'mixin_add' => function ($operand, $row, $mixinName) {
                $node = $row->getNode();
                $node->addMixin($mixinName);
            },
            'mixin_remove' => function ($operand, $row, $mixinName) {
                $node = $row->getNode();

                if ($node->isNodeType($mixinName)) {
                    $node->removeMixin($mixinName);
                }
            },
        );

        $this->functionMapSet = array(
            'array_replace' => function ($operand, $row, $v, $x, $y) {
                $operand->validateScalarArray($v);
                foreach ($v as $key => $value) {
                    if ($value === $x) {
                        $v[$key] = $y;
                    }
                }

                return $v;
            },
            'array_remove' => function ($operand, $row, $v, $x) {
                foreach ($v as $key => $value) {
                    if ($value === $x) {
                        unset($v[$key]);
                    }
                }

                return array_values($v);
            },
            'array_append' => function ($operand, $row, $v, $x) {
                $operand->validateScalarArray($v);
                $v[] = $x;

                return $v;
            },
            'array' => function () {
                $values = func_get_args();

                // first argument is the operand
                array_shift($values);
                // second is the row
                array_shift($values);

                return $values;
            },
            'array_replace_at' => function ($operand, $row, $current, $index, $value) {
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
    public function updateNodeSet(RowInterface $row, $propertyData)
    {
        $node = $row->getNode($propertyData['selector']);
        $value = $propertyData['value'];

        if ($value instanceof FunctionOperand) {
            $value = $propertyData['value'];
            $value = $value->execute($this->functionMapSet, $row);
        }

        $node->setProperty($propertyData['name'], $value);
    }

    public function updateNodeApply(RowInterface $row, FunctionOperand $apply)
    {
        if (!$apply instanceof FunctionOperand) {
            throw new \InvalidArgumentException(
                'Was expecting a function operand but got something else'
            );
        }

        $apply->execute($this->functionMapApply, $row);
    }

    private function handleFunction($row, $propertyData)
    {
        return $value;
    }
}
