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

    public function replaceColumnOperands(RowInterface $row)
    {
        foreach ($this->arguments as $key => $value) {
            if ($value instanceof ColumnOperand) {
                $this->arguments[$key] = $row->getNode($value->getSelectorName())->getPropertyValue($value->getPropertyName());
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
