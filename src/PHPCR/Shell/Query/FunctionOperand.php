<?php

namespace PHPCR\Shell\Query;

use PHPCR\Shell\Query\ColumnOperand;
use PHPCR\Query\RowInterface;

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

    public function execute($functionMap, $row, $value)
    {
        $this->replaceColumnOperands($functionMap, $row);

        $functionName = $value->getFunctionName();
        if (!isset($functionMap[$functionName])) {
            throw new InvalidQueryException(sprintf('Unknown function "%s", known functions are "%s"',
                $functionName,
                implode(', ', array_keys($functionMap))
            ));
        }

        $callable = $functionMap[$functionName];
        $args = $value->getArguments();
        array_unshift($args, $this);
        $value = call_user_func_array($callable, $args);

        return $value;
    }

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


    public function getFunctionName()
    {
        return $this->functionName;
    }

    public function getArguments() 
    {
        return $this->arguments;
    }
}
