<?php

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
