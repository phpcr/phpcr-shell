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
