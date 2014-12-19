<?php

namespace PHPCR\Shell\Config;

/**
 * Configuration profile object
 */
class Config implements \ArrayAccess, \Iterator 
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function offsetSet($offset, $value)
    {
        throw new \InvalidArgumentException(sprintf(
            'Setting values not permitted on configuration objects (trying to set "%s" to "%s"',
            $offset, $value
        ));
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            $value = $this->data[$offset];
            if (is_array($value)) {
                return new self($value);
            } else {
                return $value;
            }
        }
    }

    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function rewind()
    {
        return reset($this->data);
    }

    public function valid()
    {
        return current($this->data);
    }
}
