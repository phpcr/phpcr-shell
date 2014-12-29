<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Transport;

class TransportRegistry implements TransportRegistryInterface
{
    protected $transports = array();

    public function register(TransportInterface $transport)
    {
        $this->transports[$transport->getname()] = $transport;
    }

    public function getTransportNames()
    {
        return array_keys($this->transports);
    }

    public function getTransport($name)
    {
        return $this->transports[$name];
    }
}
