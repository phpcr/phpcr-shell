<?php

namespace PHPCR\Shell\Transport;

/**
 * Interface for transport factory
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface TransportFactoryInterface
{
    /**
     * Return all of the registered transport names
     *
     * @return array
     */
    public function getTransportNames();

    /**
     * Return the transport with the given name
     *
     * @param string $name
     *
     * @return PHPCR\Shell\Transport\Transport\TransportInterface
     */
    public function getTransport($name);
}
