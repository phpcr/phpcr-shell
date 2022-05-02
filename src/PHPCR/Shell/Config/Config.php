<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Config;

/**
 * Configuration profile object.
 */
class Config implements \ArrayAccess, \Iterator
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Setting values not permitted on configuration objects (trying to set "%s" to "%s"',
            $offset,
            $value
        ));
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            $value = $this->data[$offset];

            if (is_array($value)) {
                return new self($value);
            }

            return $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->data);
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        return reset($this->data);
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return current($this->data);
    }
}
