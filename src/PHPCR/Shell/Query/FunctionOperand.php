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
use PHPCR\Query\InvalidQueryException;

/**
 * Simple class to represent function operands in query
 * evaluations.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class FunctionOperand
{
    private $functionName;
    private $arguments;

    public function __construct($functionName, $arguments)
    {
        $this->functionName = $functionName;
        $this->arguments = $arguments;
    }

    /**
     * Replace the Operand objects with their evaluations
     *
     * @param array Array of function closures
     * @param RowInterface $row
     */
    private function replaceColumnOperands($functionMap, RowInterface $row)
    {
        foreach ($this->arguments as $key => $value) {
            if ($value instanceof ColumnOperand) {
                $this->arguments[$key] = $row->getNode($value->getSelectorName())->getPropertyValue($value->getPropertyName());
            }

            if ($value instanceof FunctionOperand) {
                $this->arguments[$key] = $value->execute($functionMap, $row, $value);
            }
        }
    }

    /**
     * Evaluate the result of the function
     *
     * @param array Array of function closures
     * @param RowInterface $row
     */
    public function execute($functionMap, $row)
    {
        $this->replaceColumnOperands($functionMap, $row);

        $functionName = $this->getFunctionName();
        if (!isset($functionMap[$functionName])) {
            throw new InvalidQueryException(sprintf('Unknown function "%s", known functions are "%s"',
                $functionName,
                implode(', ', array_keys($functionMap))
            ));
        }

        $callable = $functionMap[$functionName];
        $args = $this->getArguments();
        array_unshift($args, $row);
        array_unshift($args, $this);
        $value = call_user_func_array($callable, $args);

        return $value;
    }

    /**
     * Used as callback for closure functions
     *
     * @param array Array of values which must be scalars
     * @throws InvalidArgumentException
     */
    public function validateScalarArray($array)
    {
        if (!is_array($array)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected array value, got: %s',
                var_export($array, true)
            ));
        }

        foreach ($array as $key => $value) {
            if (false == is_scalar($value)) {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot use an array as a value in a multivalue property. Value was: %s',
                    var_export($array, true)
                ));
            }
        }
    }

    /**
     * Return the name of the function to execute
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * Return the functions arguments
     *
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
