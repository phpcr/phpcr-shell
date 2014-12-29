<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Config;

/**
 * Configuration profile object
 */
class Profile
{
    protected $profile = array(
        'transport' => array(),
        'phpcr' => array(),
    );

    protected $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * Return the array data for this profile
     *
     * @return array
     */
    public function toArray()
    {
        return $this->profile;
    }

    protected function validateDomain($domain)
    {
        if (!array_key_exists($domain, $this->profile)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown profile domain "%s", can only use one of: %s',
                $domain, implode(', ', array_keys($this->profile))
            ));
        }
    }

    /**
     * Set a domain configuration
     *
     * @param string     $domain
     * @param $key
     * @param array|null $value
     *
     */
    public function set($domain, $key, $value = null)
    {
        $this->validateDomain($domain);
        if (null !== $value) {
            $this->profile[$domain][$key] = $value;
        } else {
            $this->profile[$domain] = $key;
        }
    }

    /**
     * Get a domain configuration
     *
     * @param string $domain
     * @param string $key
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function get($domain, $key = null)
    {
        $this->validateDomain($domain);

        if (null === $key) {
            return $this->profile[$domain];
        }

        if (!isset($this->profile[$domain][$key])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown key "%s" for profile domain "%s"',
                $key, $domain
            ));
        }

        return $this->profile[$domain][$key];
    }

    /**
     * Return the name of this profile
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
