<?php

namespace PHPCR\Shell\Query;

/**
 * Simple class to represent column operands in query
 * evaluations.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ColumnOperand
{
    private $selectorName;
    private $propertyName;

    public function __construct($selectorName, $propertyName)
    {
        $this->selectorName = $selectorName;
        $this->propertyName = $propertyName;
    }

    public function getSelectorName() 
    {
        return $this->selectorName;
    }

    public function getPropertyName() 
    {
        return $this->propertyName;
    }
}
